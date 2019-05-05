<?php

namespace fortress\core\database;

class DatabaseConfiguration {

    private $driver;
    private $host;
    private $port;

    private $databaseName;

    private $username;
    private $password;

    public function __construct(array $configuration) {
        $this->host = $configuration["DB_HOST"] ?? "localhost";
        $this->port = $configuration["DB_PORT"] ?? null;
        $this->username = $configuration["DB_USERNAME"] ?? null;
        $this->password = $configuration["DB_PASSWORD"] ?? null;
        $this->driver = $configuration["DB_DRIVER"] ?? null;
        $this->databaseName = $configuration["DB_NAME"] ?? null;
    }

    public function host() {
        return $this->host;
    }

    public function port() {
        return $this->port;
    }

    public function username() {
        return $this->username;
    }
    
    public function password() {
        return $this->password;
    }

    public function databaseName() {
        return $this->databaseName;
    }

    public function driverName() {
        return $this->driver;
    }
}