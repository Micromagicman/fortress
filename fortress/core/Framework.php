<?php

namespace fortress\core;

use fortress\core\configurator\Configurator;
use fortress\core\di\ContainerInterface;
use fortress\core\di\DependencyNotFoundException;

use Symfony\Component\HttpFoundation\Request;

class Framework {

    private $configuration;

    private $container;

    public function __construct(Configurator $conf, ContainerInterface $ci) {
        $this->container = $ci;
        $conf->initializeContainer($this->container);
        $conf->initializeRouter($this->container->get("router")->getRouteCollection());
    }

    public function run(Request $request) {
        $router = $this->container->get("router");
        var_dump($router);
        echo "fortress is running";
    }
}
