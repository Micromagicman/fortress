<?php

namespace fortress\core\database\driver;

use fortress\core\database\DatabaseConfiguration;
use fortress\core\exception\DatabaseConnectionError;
use PDO;

abstract class Driver {

    public const POSTGRES = "pgsql";

    /**
     * Создание драйвера по переданному имени
     * @param string $name
     * @return PostgreSqlDriver
     * @throws DatabaseConnectionError
     */
    public static function createDriver(string $name) {
        if (self::POSTGRES === $name) {
            return new PostgreSqlDriver();
        }
        throw new DatabaseConnectionError(sprintf("Could not find driver %s", $name));
    }

    public function createConnection(DatabaseConfiguration $conf) {
        return new PDO(
            $this->createDsn($conf),
            $conf->username(),
            $conf->password()
        );
    }

    protected abstract function createDsn(DatabaseConfiguration $conf);
}