<?php

namespace jhuta\phpmvc;

use jhuta\phpmvc\Controller;
use jhuta\phpmvc\Exceptions\NotFoundException;

class Router {
  public Request $request;
  public Response $response;
  protected array $routes = [];

  public function __construct(Request $request, Response $response) {
    $this->request = $request;
    $this->response = $response;
  }

  public function get(string $url, $callback) {
    $this->routes['get'][$url] = $callback;
  }
  public function post(string $url, $callback) {
    $this->routes['post'][$url] = $callback;
  }

  public function resolve() {
    $method = $this->request->method();
    $path = $this->request->getPath();
    $callback = $this->routes[$method][$path] ?? false;
    if ($callback === false) {
      throw new NotFoundException();
    }
    if (is_string($callback)) {
      return Application::$app->view->renderView($callback);
    }
    if (is_array($callback)) {
      /** @var $controller Controller */
      $controller = new $callback[0];
      $controller->action = $callback[1];
      Application::$app->controller = $controller;
      $middlewares = $controller->getMiddlewares();
      foreach ($middlewares as $middleware) {
        $middleware->execute();
      }
      $callback[0] = $controller;
    }
    return call_user_func($callback, $this->request, $this->response);
  }
}
