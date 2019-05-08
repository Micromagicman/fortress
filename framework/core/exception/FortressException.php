<?php

namespace fortress\core\exception;

use Exception;
use Throwable;

class FortressException extends Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}