<?php

namespace fortress\core\exception;

class DatabaseQueryException extends FortressException {

    private $query;

    public function __construct(string $message, string $query) {
        parent::__construct("$message on: '$query'");
        $this->query = $query;
    }

    public function getQuery() {
        return $this->query;
    }
}