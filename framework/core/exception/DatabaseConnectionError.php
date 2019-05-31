<?php

namespace fortress\core\exception;

use Exception;

class DatabaseConnectionError extends FortressException {

    public function __construct(string $message, Exception $cause = null) {
        parent::__construct("Database connection error: " . $message, $cause->getCode(), $cause);
    }
}