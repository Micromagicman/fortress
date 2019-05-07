<?php

namespace fortress\core\configurator;

use fortress\core\di\ContainerInterface;
use fortress\core\database\DatabaseConfiguration;
use fortress\core\database\Database;
use fortress\core\router\RouteCollection;
use fortress\core\router\Router;

class Configurator {

    public function initializeContainer(ContainerInterface $c) {
        // parameters
        $this->initializeParameters($c);
        // core services
        $this->initializeRouter($c);
        $this->initializeDatabase($c);
        // custom services
        $this->intializeServices($c);
    }

    private function initializeParameters(ContainerInterface $c) {
        $parameterInitializer = require_once  "../config/parameters.php";
        $parameterInitializer($c);
    }

    private function intializeServices(ContainerInterface $c) {
        $serviceInitializer = require_once "../config/services.php";
        $serviceInitializer($c);
    }

    private function initializeRouter(ContainerInterface $c) {
        $initializer = require_once "../config/routes.php";
        $c->set("router", Router::class);

        $routeCollection = $c->get("router")->getRouteCollection();
        if (is_array($initializer)) {
            foreach ($initializer as $routeInitializer) {
                $collection = new RouteCollection();
                $routeInitializer($collection);
                $routeCollection->addCollection($collection);
            }
        } else if (is_callable($initializer)) {
            $initializer($routeCollection);
        }
    }

    private function initializeDatabase(ContainerInterface $c) {
        $config = require_once "../config/database.php";
        $databaseConfiguration = new DatabaseConfiguration($config);
        $database = new Database($databaseConfiguration);
        $c->set("db.configuration", $databaseConfiguration);
        $c->set("db.connection", $database);
    }
}