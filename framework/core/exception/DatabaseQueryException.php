<?php

namespace fortress\core\exception;

use PDOStatement;

class DatabaseQueryException extends FortressException {

    private $query;

    public function __construct(string $message, PDOStatement $pdoStatement) {
        parent::__construct(
            sprintf(
                "%s on: '%s'. PDO message: %s",
                $message,
                $pdoStatement->queryString,
                $pdoStatement->errorInfo()[2]
            ),
            $pdoStatement->errorCode());
        $this->query = $pdoStatement->queryString;
    }

    public function getQuery() {
        return $this->query;
    }
}