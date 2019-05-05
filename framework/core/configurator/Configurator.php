<?php

namespace fortress\core\configurator;

use fortress\core\di\ContainerInterface;
use fortress\core\router\RouteCollection;
use fortress\core\database\DatabaseConfiguration;
use fortress\core\database\Database;

class Configurator {

    public function initializeContainer(ContainerInterface $c) {
        $serviceInitializer = require_once "../config/services.php";
        $parameterInitializer = require_once  "../config/parameters.php";
        $parameterInitializer($c);
        $serviceInitializer($c);
    }

    public function initializeRouter(RouteCollection $rc) {
        $initializer = require_once "../config/routes.php";
        $initializer($rc);
    }

    public function initializeDatabase(ContainerInterface $c) {
        $config = require_once "../config/database.php";
        $databaseConfiguration = new DatabaseConfiguration($config);
        $database = new Database($databaseConfiguration);
        $c->set("db.configuration", $databaseConfiguration);
        $c->set("db.connection", $database);
    }
}