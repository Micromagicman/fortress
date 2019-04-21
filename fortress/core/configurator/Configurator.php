<?php

namespace fortress\core\configurator;

use fortress\core\di\ContainerInterface;

class Configurator {

    public function initializeContainer(ContainerInterface $c) {
        $initializer = require_once "../config/services.php";
        $initializer($c);
    }
}