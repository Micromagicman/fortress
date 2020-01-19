<?php

namespace fortress\core;

use fortress\command\Command;
use fortress\core\di\Invoker;
use fortress\core\exception\FortressException;
use fortress\core\router\Route;
use fortress\core\router\Router;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * Обработка HTTP-запроса
     * @param ServerRequestInterface $request
     * @return HtmlResponse|mixed
     */
    public function handleHttpRequest(ServerRequestInterface $request) {
        try {
            $route = $this->findRoute($request);
            $response = $this->buildAndInvokeController($route, $request);
            if (!($response instanceof ResponseInterface)) {
                throw new FortressException(
                    sprintf(
                        "Controller method should return instance of %s class, %s given",
                        ResponseInterface::class,
                        is_object($response) ? get_class($response) : gettype($response)
                    )
                );
            }
            return $response;
        } catch (FortressException $e) {
            return new HtmlResponse(sprintf(
                "%s: %s",
                get_class($e),
                $e->getMessage()
            ));
        }
    }

    /**
     * Обработка консольной команды
     * @param Command $command
     */
    public function handleCommand(Command $command) {
        $command->run();
    }

    private function findRoute(ServerRequestInterface $request) {
        $router = $this->container->get(Router::class);
        return $router->match(
            $request->getUri()->getPath(),
            $request->getMethod()
        );
    }

    private function buildController(Route $route) {
        $controllerClass = $route->getControllerClass();
        $controller = $this->container->get($controllerClass);
        if (null === $controller) {
            throw new FortressException("Controller '" . $controllerClass . "' not found");
        }
        return $controller;
    }

    private function buildAndInvokeController(Route $route, ServerRequestInterface $request) {
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
