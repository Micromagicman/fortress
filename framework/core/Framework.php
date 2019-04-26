<?php

namespace fortress\core;

use fortress\core\configurator\Configurator;
use fortress\core\di\ContainerInterface;
use fortress\core\di\DependencyNotFoundException;
use fortress\core\router\Route;
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
            $response = $this->buildAndInvokeController($route);
            return $response;
        } catch (RouteNotFoundException $e) {
            // NotFoundReponse;
            echo "404";
        }
    }

    private function findRoute(Request $request) {
        $router = $this->container->get("router");
        return $router->match($request);
    }

    private function buildController(Route $route) {
        return $this->container->build(
            $route->getControllerClass(),
            [$this->container]
        );
    }

    private function invokeController($controller, string $methodName) {
        return $this->container->invoke($controller, $methodName);
    }

    private function buildAndInvokeController(Route $route) {
        $controller = $this->buildController($route);
        $methodName = $route->getActionName();
        return $this->invokeController($controller, $methodName);
    }
}
