<?php

namespace fortress\core\di;

use Exception;

class MethodNotExistException extends Exception {
    public function __construct(string $methodName) {
        parent::__construct("Action '$methodName' does not exist");
    }
}