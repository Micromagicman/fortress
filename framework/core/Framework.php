<?php

namespace fortress\core;

use fortress\core\configurator\Configurator;
use fortress\core\exception\FortressException;
use fortress\core\exception\RouteNotFound;
use fortress\core\http\response\ErrorResponse;
use fortress\core\router\Route;

use fortress\core\router\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            $response = $this->buildAndInvokeController($route);
            if (!($response instanceof Response)) {
                throw new FortressException(
                    sprintf(
                        "Controller method should return instance of %s class, %s given",
                        "Symfony\\Component\\HttpFoundation\\Response",
                        is_object($response) ? get_class($response) : gettype($response)
                    )
                );
            }
            return $response;
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
        $controller = $this->container->build($controllerClass);
        if (null === $controller) {
            throw new FortressException("Controller '" . $controllerClass . "' not found");
        }
        return $controller;
    }

    private function buildAndInvokeController(Route $route) {
        $request = $this->container->get(Request::class);
        $middlewareClass = $route->getMiddlewareClass();
        $controllerClosure = $this->createControllerInvokeClosure($route);
        if (null !== $middlewareClass) {
            $middleware = $this->container->build($middlewareClass);
            return $middleware->handle($controllerClosure);
        }
        return $controllerClosure($request);
    }

    private function createControllerInvokeClosure($route) {
        return function() use ($route) {
            return $this->container->invoke(
                $this->buildController($route),
                $route->getActionName(),
                $route->getUriVariables()
            );
        };
    }
}
