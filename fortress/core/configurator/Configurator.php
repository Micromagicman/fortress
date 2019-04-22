<?php

namespace fortress\core\configurator;

use fortress\core\di\ContainerInterface;
use fortress\core\router\RouteCollection;

class Configurator {

    public function initializeContainer(ContainerInterface $c) {
        $initializer = require_once "../config/services.php";
        $initializer($c);
    }

    public function initializeRouter(RouteCollection $rc) {
        $initializer = require_once "../config/routes.php";
        $initializer($rc);
    }
}