<?php

namespace fortress\core\database;

use fortress\core\database\driver\Driver;
use fortress\core\exception\DatabaseConnectionError;
use fortress\core\exception\DatabaseQueryException;
use fortress\core\exception\FortressException;
use PDOException;

class DatabaseConnection {

    private $driver;

    private $connection;

    public function __construct(DatabaseConfiguration $configuration = null) {
        try {
            if (null == $configuration) {
                throw new FortressException("There is no database configuration in the framework settings");
            }
            $this->driver = Driver::createDriver($configuration->driverName());
            $this->connection = $this->driver->createConnection($configuration);
        } catch (PDOException $e) {
            throw new DatabaseConnectionError("Failed to create PDO connection", $e);
        } catch (DatabaseConnectionError $e) {
            throw $e;
        }
    }

    public function query(string $sql, array $binds = []) {
        $statement = $this->connection->prepare($sql);
        if (!$statement) {
            throw new DatabaseQueryException("Error preparing query", $sql);
        }

        if (!$statement->execute($binds)) {
            throw new DatabaseQueryException("Error performing query", $sql);
        }

        return $statement;
    }
}