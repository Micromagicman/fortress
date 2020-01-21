<?php

namespace fortress\core\exception;

class RouteNotFound extends FortressException {

    private string $path;

    private string $method;

    public function __construct(string $method, string $path) {
        parent::__construct(sprintf("Route not found for %s %s", $method, $path));
    }

    public function getMethod() {
        return $this->method;
    }

    public function getPath() {
        return $this->path;
    }
}