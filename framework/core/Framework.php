<?php

namespace fortress\core;

use fortress\core\configurator\Configurator;
use fortress\core\exception\FortressException;
use fortress\core\http\response\ErrorResponse;
use fortress\core\router\Route;
use fortress\core\router\RouteNotFound;

use Symfony\Component\HttpFoundation\Request;

class Framework {

    private $container;

    public function __construct(Configurator $conf, $container) {
        $this->container = $container;
        $conf->initializeContainer($this->container);
    }

    public function run(Request $request) {
        try {
            $route = $this->findRoute($request);
            return $this->buildAndInvokeController($route);
        } catch (RouteNotFound $e) {
            return ErrorResponse::NotFound($e, $this->container);
        } catch (FortressException $e) {
            return ErrorResponse::ServerError($e, $this->container);
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

    private function invokeController($controller, string $methodName, array $arguments) {
        return $this->container->invoke($controller, $methodName, $arguments);
    }

    private function buildAndInvokeController(Route $route) {
        return $this->invokeController(
            $this->buildController($route),
            $route->getActionName(),
            $route->getUriVariables()
        );
    }
}
