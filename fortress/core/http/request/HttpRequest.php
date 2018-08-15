<?php

namespace fortress\core\http\request;

class HttpRequest {

  private $request_method;

  private $uri;

  public function __construct() {
    $this->request_method = $_SERVER["REQUEST_METHOD"];
    $this->request_uri = $_SERVER["REQUEST_URI"];
  }

  public function method() {
    return $this->request_method;
  }

  public function uri() {
    return $this->uri;
  }
}
