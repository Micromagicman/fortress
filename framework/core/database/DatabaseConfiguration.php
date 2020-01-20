<?php

namespace fortress\core\database;

use fortress\core\configuration\Configuration;
use fortress\core\configuration\ConfigurationBag;
use Psr\Container\ContainerInterface;

class DatabaseConfiguration {

    private $driver;

    private $host;

    private $port;

    private $databaseName;

    private $username;

    private $password;

    public function __construct(ContainerInterface $container) {
        $this->driver = $container->get(Configuration::DATABASE_DRIVER_KEY);
        $this->host = $container->get(Configuration::DATABASE_HOST_KEY);
        $this->port = $container->get(Configuration::DATABASE_PORT_KEY);
        $this->username = $container->get(Configuration::DATABASE_USERNAME_KEY);
        $this->password = $container->get(Configuration::DATABASE_PASSWORD_KEY);
        $this->databaseName = $container->get(Configuration::DATABASE_NAME_KEY);
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