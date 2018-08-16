<?php

namespace fortress\core;

use fortress\core\http\request\HttpRequest;
use fortress\core\exception\FortressException;
use fortress\core\router\Router;

use fortress\core\logger\HtmlLogger;

class Framework {

  private $app_name;

  public function __construct($app_name) {
    $this->app_name = $app_name;
  }

  public function run() {
    try {
      $router = new Router();
    } catch (FortressException $e) {
      $logger = new HtmlLogger();
      $logger->printException($e);
    }
  }
}
