<?php

namespace fortress\core\configurator;

use fortress\core\di\ContainerInterface;
use fortress\core\router\RouteCollection;

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
}