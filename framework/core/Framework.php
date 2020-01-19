<?php

namespace fortress\core;

use fortress\command\Command;
use fortress\core\di\Invoker;
use fortress\core\exception\FortressException;
use fortress\core\exception\RouteNotFound;
use fortress\core\http\response\ErrorResponse;
use fortress\core\router\Route;
use fortress\core\router\Router;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Framework
 * @package fortress\core
 */
class Framework {

    /**
     * Контейнер внедрения зависимостей
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param $runnable
     * @return ErrorResponse|mixed
     */
    public function run($runnable) {
        if ($runnable instanceof Command) {
            return $this->runFromCommand($runnable);
        } else if ($runnable instanceof Request) {
            return $this->runFromHttpRequest($runnable);
        }
        throw new InvalidArgumentException(
            sprintf(
                "Incorrect runnable instance %s or %s expected, %s given",
                Request::class,
                Command::class,
                is_object($runnable) ? get_class($runnable) : gettype($runnable)
            )
        );
    }

    private function runFromCommand(Command $command) {
        $command->run();
        return true;
    }

    private function runFromHttpRequest(Request $request) {
        try {
            $route = $this->findRoute($request);
            $response = $this->buildAndInvokeController($route, $request);
            if (!($response instanceof Response)) {
                throw new FortressException(
                    sprintf(
                        "Controller method should return instance of %s class, %s given",
                        Response::class,
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
        $controller = $this->container->get($controllerClass);
        if (null === $controller) {
            throw new FortressException("Controller '" . $controllerClass . "' not found");
        }
        return $controller;
    }

    private function buildAndInvokeController(Route $route, Request $request) {
        $middlewareClass = $route->getMiddleware();
        $controllerClosure = $this->createControllerInvokeClosure($route);
        if (null !== $middlewareClass) {
            $middleware = $this->container->build($middlewareClass);
            return $middleware->handle($controllerClosure);
        }
        return $controllerClosure($request);
    }

    private function createControllerInvokeClosure($route) {
        return function () use ($route) {
            return $this->container
                ->get(Invoker::class)
                ->invoke(
                    [$this->buildController($route), $route->getActionName()],
                    $route->getUriVariables()
                );
        };
    }
}
