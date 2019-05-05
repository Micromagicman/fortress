<?php

namespace fortress\core\database\driver;

use fortress\core\database\DatabaseConfiguration;

class PostgreSqlDriver extends Driver {

    protected function createDsn(DatabaseConfiguration $conf) {
        return "pgsql:dbname=" . $conf->databaseName() . ";host=" . $conf->host() . ";port=" . $conf->port();
    }
}