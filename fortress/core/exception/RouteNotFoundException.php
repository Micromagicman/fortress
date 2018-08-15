<?php

namespace fortress\core\exception;

class RouteNotFoundException extends FortressException {

  public function __construct($uri) {
    parent::__construct("Route not found for request URI: " . $uri);
  }
}
