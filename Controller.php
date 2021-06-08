<?php

namespace jhuta\phpmvc;

use jhuta\phpmvc\Middlewares\BaseMiddleware;

class Controller {
  public string $layout = 'main';
  public string $action = '';

  /** @var \jhuta\phpmvc\Middlewares\BaseMiddleware[] */
  protected array $middlewares = [];

  public function setLayout($layout): void {
    $this->layout = $layout;
  }

  public function render($view, $params = []): string {
    return Application::$app->view->renderView($view, $params);
  }

  public function registerMiddleware(BaseMiddleware $middleware) {
    $this->middlewares[] = $middleware;
  }

  /** @var \jhuta\phpmvc\Middlewares\BaseMiddleware[] */
  public function getMiddlewares(): array {
    return $this->middlewares;
  }

  /** @var \jhuta\phpmvc\Middlewares\BaseMiddleware[] */
  public function setMiddlewares($middleware) {
    $this->middlewares[] = $middleware;
  }
}