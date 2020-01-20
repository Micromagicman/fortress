<?php

namespace fortress\core\database;

use fortress\core\configurator\ConfigurationBag;

class DatabaseConfiguration {

    private $driver;
    private $host;
    private $port;
    private $databaseName;
    private $username;
    private $password;

    private function __construct(ConfigurationBag $configuration) {
        $this->driver = $configuration->get("DB_DRIVER");
        $this->host = $configuration->get("DB_HOST", "localhost");
        $this->port = $configuration->get("DB_PORT");
        $this->username = $configuration->get("DB_USERNAME");
        $this->password = $configuration->get("DB_PASSWORD");
        $this->databaseName = $configuration->get("DB_NAME");
    }

    public static function build(ConfigurationBag $configuration) {
        if (null === $configuration || empty($configuration->items())) {
            return null;
        }
        return new DatabaseConfiguration($configuration);
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