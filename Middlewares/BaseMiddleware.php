<?php

namespace jhuta\phpmvc\Middlewares;

abstract class BaseMiddleware {
  abstract public function execute();
}