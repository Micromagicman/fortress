<?php

namespace fortress\core\database;

use fortress\core\database\driver\Driver;
use fortress\core\exception\DatabaseConnectionError;
use fortress\core\exception\DatabaseQueryException;
use fortress\core\exception\FortressException;
use PDO;
use PDOException;

class DatabaseConnection {

    private $driver;
    private $connection;

    private $lastQuery;

    public function __construct(DatabaseConfiguration $configuration = null) {
        try {
            if (null === $configuration) {
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
        $this->lastQuery = null;
        $statement = $this->connection->prepare($sql);
        if (!$statement || !$statement->execute($binds)) {
            throw new DatabaseQueryException("Error execute query", $statement);
        }
        $this->lastQuery = $statement;
        return $this;
    }

    public function fetchSingle() {
        if (null !== $this->lastQuery) {
            $result = $this->lastQuery->fetch(PDO::FETCH_OBJ);
            return $result ?? null;
        }
        return null;
    }

    public function fetchAll() {
        if (null !== $this->lastQuery) {
            $result = $this->lastQuery->fetchAll(PDO::FETCH_OBJ);
            return $result ?? [];
        }
        return [];
    }

    public function createTransaction() {
        if (!$this->connection->inTransaction()) {
            $this->connection->beginTransaction();
        }
        return $this;
    }

    public function commit() {
        if ($this->connection->inTransaction()) {
            $this->connection->commit();
        }
        return $this;
    }

    public function rollBack() {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }
        return $this;
    }
}