<?php

namespace fortress\core\database;

use fortress\core\database\driver\Driver;

class Database {

    private $driver;

    private $connection;

    public function __construct(DatabaseConfiguration $conf) {
        try {
            $this->driver = Driver::createDriver($conf->driverName());
            $this->connection = $this->driver->createConnection($conf);
        } catch (\PDOException $e) {
            throw new DatabaseNotInitialized("Failed to create PDO connection", $e);
        }
    }

    public function query(string $sql, array $binds = []) {
        $statement = $this->connection->prepare($sql);
        if (!$statement) {
            // TODO - throw
        }

        if (!$statement->execute($binds)) {
            // TODO - throw
        }

        return $statement;
    }
}