<?php

namespace fortress\core;

use fortress\core\http\request\HttpRequest;
use fortress\core\exception\FortressException;
use fortress\core\router\Router;

class Framework {

  private $app_name;

  public function __construct($app_name) {
    $this->app_name = $app_name;
  }

  public function run() {
    try {
      $router = new Router();
    } catch (FortressException $e) {
      echo $e -> getMessage();
    }
  }

}
