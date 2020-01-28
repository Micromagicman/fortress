<?php

namespace fortress\core\router\exception;

use fortress\core\exception\FortressException;

class RouteNotFound extends FortressException {
    public function __construct(string $method, string $path) {
        parent::__construct(sprintf("Route not found for %s %s", $method, $path));
    }
}