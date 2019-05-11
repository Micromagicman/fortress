<?php

namespace fortress\core\database;

use Exception;
use fortress\core\exception\FortressException;

class DatabaseNotInitialized extends FortressException {

    public function __construct(string $message, Exception $cause = null) {
        parent::__construct("Database initialization error: " . $message, $cause);
    }
}