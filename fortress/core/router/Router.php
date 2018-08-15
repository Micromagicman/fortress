<?php

namespace fortress\core\router;

use fortress\core\exception\RouteNotFoundException;

class Router {

  private $routes;

  public function __construct() {
  }

  public function route($request) {
    $uri = $request -> uri();

    foreach ($this->routes as $route) {
      // Поиск роута
    }

    return true;

    throw new RouteNotFoundException($uri);
  }

  private function initialize() {
    // Парсинг всего конфига
  }

  private function parse_route_data($data) {
    // Парсинг одного роута конфига
  }

  private function parse_union_data($data) {
    // Парсинг объединения в конфиге
  }

  private function add($route, $controller, $action, $method, $middleware) {
    $regex = $this -> create_regex($route);
    array_push($this -> routes, new Route($route, $regex, $controller, $action, $method, $middleware));
  }

  private function createRegex($uri) {
    // Составление регуляного выражения на основе конфигурационного uri
  }

  public static function redirect($location) {
    if (!headers_sent()) {
      header("Location: " . $location);
    } else {
      echo "<script type=\"text/javascript\">";
      echo "window.location.href=\"" . $location . "\"";
      echo "</script>";
      echo "<noscript>";
      echo "<meta http-equiv=\"refresh\" content=\"0;url=" . $location . "\"/>";
      echo "</noscript>";
    }
  }
}
