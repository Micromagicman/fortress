<?php

namespace fortress\core\exception;

class DatabaseConnectionError extends FortressException {
    public function __construct(string $message) {
        parent::__construct(sprintf("Database connection error: %s", $message));
    }
}