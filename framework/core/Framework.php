<?php

namespace fortress\core;

use fortress\core\configurator\Configurator;
use fortress\core\exception\FortressException;
use fortress\core\exception\RouteNotFound;
use fortress\core\http\response\ErrorResponse;
use fortress\core\router\Route;

use fortress\core\router\Router;
use Symfony\Component\HttpFoundation\Request;

class Framework {

    private $container;
    private $configurator;

    public function __construct(Configurator $configurator, $container) {
        $this->container = $container;
        $this->configurator = $configurator;
    }

    public function run(Request $request) {
        try {
            $this->configurator->initializeContainer($this->container, $request);
            $route = $this->findRoute($request);
            return $this->buildAndInvokeController($route);
        } catch (RouteNotFound $e) {
            return ErrorResponse::NotFound($e, $this->container);
        } catch (FortressException $e) {
            return ErrorResponse::ServerError($e, $this->container);
        }
    }

    private function findRoute(Request $request) {
        $router = $this->container->get(Router::class);
        return $router->match($request);
    }

    private function buildController(Route $route) {
        $controllerClass = $route->getControllerClass();
        $controller = $this->container->build($controllerClass, [$this->container]);
        if (null === $controller) {
            throw new FortressException("Controller '" . $controllerClass . "' not found");
        }
        return $controller;
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
