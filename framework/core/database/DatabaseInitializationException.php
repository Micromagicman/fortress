<?php

namespace fortress\core\database;

class DatabaseInitializationException extends \Exception {

    public function __construct(string $message, \Exception $cause = null) {
        parent::__construct("Database initialization error: " . $message, $cause);
    }
}