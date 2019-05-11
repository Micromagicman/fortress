<?php

namespace fortress\core\exception;

use Exception;

class DatabaseNotInitialized extends FortressException {

    public function __construct(string $message, Exception $cause = null) {
        parent::__construct("Database initialization error: " . $message, $cause);
    }
}