<?php

namespace fortress\core\controller;

use fortress\core\ActionPipeline;
use fortress\core\middleware\BeforeAction;
use fortress\core\router\exception\RouteNotFound;
use fortress\core\router\Route;
use fortress\core\router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ControllerAction
 * @package fortress\core\controller
 */
class ControllerAction extends BeforeAction {

    private Router $router;

    public function __construct(ContainerInterface $container, Router $router) {
        parent::__construct($container);
        $this->router = $router;
    }

    /**
     * Обработка запроса в контроллере
     * Перед вызовом контроллера выполняются все action-ы, определенные как предварительные
     * в соответствующем контроллере маршруте. Тоже самое происходит и для последующих action-ов
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return ResponseInterface
     * @throws RouteNotFound
     */
    protected function handleRequest(ServerRequestInterface $request, callable $next) {
        /** @var Route $route */
        $route = $this->resolveRoute($request);
        $request = $this->getActionsPipeline($route->getBeforeActions())->run($request);
        // Вызов контроллера
        $response = $this->resolveController($route)->handle($request);
        return $this->getActionsPipeline($route->getAfterActions())->run($response, $next);
    }

    /**
     * Поиск маршрута, соответсвующего текущему HTTP-запросу
     * @param ServerRequestInterface $request
     * @return Route
     * @throws RouteNotFound
     */
    private function resolveRoute(ServerRequestInterface $request) {
        return $this->router->match(
            $request->getUri()->getPath(),
            $request->getMethod()
        );
    }

    /**
     * @param Route $route
     * @return Controller
     */
    private function resolveController(Route $route) {
        return $this->getContainer()->get($route->getControllerClass());
    }

    /**
     * Создание конейера для обработки предварительных/последующих action-ов
     * @param array $actions
     * @return ActionPipeline
     */
    private function getActionsPipeline(array $actions) {
        $pipeline = new ActionPipeline($this->getContainer());
        foreach ($actions as $action) {
            $pipeline->pipe($action);
        }
        return $pipeline;
    }
}