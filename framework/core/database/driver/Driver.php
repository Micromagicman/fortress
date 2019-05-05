<?php

namespace fortress\core\database\driver;

use fortress\core\database\DatabaseConfiguration;

abstract class Driver {

    public static function createDriver(string $name) {
        switch ($name) {
            case "pgsql": 
                return new PostgreSqlDriver();
            default: 
                throw new DatabaseInitializationException("Could not find driver " . $name);
        }
    }

    public function createConnection(DatabaseConfiguration $conf) {
        return new \PDO(
            $this->createDsn($conf),
            $conf->username(),
            $conf->password()
        );
    }

    protected abstract function createDsn(DatabaseConfiguration $conf);
}