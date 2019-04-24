<?php

namespace fortress\core;

use fortress\core\configurator\Configurator;
use fortress\core\di\ContainerInterface;
use fortress\core\di\DependencyNotFoundException;
use fortress\core\router\RouteNotFoundException;

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
        try {
            $this->container->set("request", $request);
            $route = $this->findRoute($request);
            var_dump($route);
            echo "fortress v" . $this->container->getParameter("fortress.version") . " is running";
        } catch (RouteNotFoundException $e) {
            // NotFoundReponse;
            echo "404";
        }
    }

    private function findRoute(Request $request) {
        $router = $this->container->get("router");
        return $router->match($request);
    }
}
