<?php

namespace fortress\core;

use Exception;
use fortress\core\configurator\Configurator;
use fortress\core\di\ContainerInterface;
use fortress\core\di\MethodNotExistException;
use fortress\core\http\response\NotFoundResponse;
use fortress\core\router\Route;
use fortress\core\router\RouteNotFoundException;

use fortress\core\view\PhpView;
use Symfony\Component\HttpFoundation\Request;

class Framework {

    private $container;

    public function __construct(Configurator $conf, ContainerInterface $ci) {
        $this->container = $ci;
        $conf->initializeContainer($this->container);
    }

    public function run(Request $request) {
        try {
            $this->container->set("request", $request);
            $route = $this->findRoute($request);
            $response = $this->buildAndInvokeController($route);
            return $response;
        } catch (RouteNotFoundException | MethodNotExistException $e) {
            return new NotFoundResponse($this->handleNotFound($e));
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

    private function handleNotFound(Exception $e) {
        $notFoundTemplatePath = $this->container->getParameter("template.404");
        $content = $e->getMessage();
        if (null != $notFoundTemplatePath) {
            $view = new PhpView($notFoundTemplatePath);
            $content = $view->render(["exception" => $e]);
        }
        return $content;
    }
}
