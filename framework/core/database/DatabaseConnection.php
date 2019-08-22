<?php

namespace fortress\core\database;

use fortress\core\database\driver\Driver;
use fortress\core\exception\DatabaseConnectionError;
use fortress\core\exception\DatabaseQueryException;
use fortress\core\exception\FortressException;
use PDO;
use PDOException;

class DatabaseConnection {

    private $configuration;
    private $driver;
    private $connection;

    private $lastQuery;

    public function __construct(DatabaseConfiguration $configuration = null) {
        $this->configuration = $configuration;
    }

    public function query(string $sql, array $binds = []) {
        if (null === $this->connection) {
            $this->connect();
        }
        $this->lastQuery = null;
        $statement = $this->connection->prepare($sql);
        if (!$statement || !$statement->execute($binds)) {
            throw new DatabaseQueryException("Error execute query", $statement);
        }
        $this->lastQuery = $statement;
        return $this;
    }

    public function fetchSingle($fetchType = PDO::FETCH_OBJ) {
        if (null !== $this->lastQuery) {
            $result = $this->lastQuery->fetch($fetchType);
            return $result ?? null;
        }
        return null;
    }

    public function fetchAll($fetchType = PDO::FETCH_OBJ) {
        if (null !== $this->lastQuery) {
            $result = $this->lastQuery->fetchAll($fetchType);
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

    private function connect() {
        try {
            if (null === $this->configuration) {
                throw new FortressException("There is no database configuration in the framework settings");
            }
            $this->driver = Driver::createDriver($this->configuration->driverName());
            $this->connection = $this->driver->createConnection($this->configuration);
        } catch (PDOException $e) {
            throw new DatabaseConnectionError("Failed to create PDO connection", $e);
        } catch (DatabaseConnectionError $e) {
            throw $e;
        }
    }
}