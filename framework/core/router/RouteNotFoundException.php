<?php

namespace fortress\core\router;

use Exception;

class RouteNotFoundException extends Exception {
    public function __construct($message) {
        parent::__construct("Сannot find route for '$message'");
    }
}