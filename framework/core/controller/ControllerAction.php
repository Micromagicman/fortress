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
     * @throws UnexpectedResponseException
     */
    protected function handleRequest(ServerRequestInterface $request, callable $next) {
        /** @var Route $route */
        $route = $this->resolveRoute($request);
        $beforeResult = $this->processBeforeActions($request, $route->getBeforeActions());
        if ($beforeResult instanceof ResponseInterface) {
            return $beforeResult;
        }
        foreach ($route->getPathVariables() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        // Вызов контроллера
        $response = $this->validateResponse($this->resolveController($route)->handle($request));
        return $this->processAfterActions($response, $route->getAfterActions(), $next);
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

    /**
     * Обработка действия после вызова кода контроллера
     * @param ResponseInterface $response
     * @param array $actions
     * @param callable $next
     * @return mixed
     * @throws UnexpectedResponseException
     */
    private function processAfterActions(ResponseInterface $response, array $actions, callable $next) {
        $result = $this->getActionsPipeline($actions)->run($response, $next);
        return $this->validateResponse($result);
    }

    /**
     * Обработка действия до вызова кода контроллера
     * @param ServerRequestInterface $request
     * @param array $actions
     * @return mixed
     * @throws UnexpectedResponseException
     */
    private function processBeforeActions(ServerRequestInterface $request, array $actions) {
        $pipeline = $this->getActionsPipeline($actions);
        $result = $pipeline->run($request);
        if (!($result instanceof ServerRequestInterface)) {
            return $this->validateResponse($result);
        }
        return $result;
    }

    /**
     * Проверка корректности возвращаемого из action-ов HTTP ответа
     * @param $actionResult
     * @return mixed
     * @throws UnexpectedResponseException
     */
    private function validateResponse($actionResult) {
        if ($actionResult instanceof ResponseInterface) {
            return $actionResult;
        }
        throw new UnexpectedResponseException(sprintf(
            "Unexpected response type. Implementation of %s expected, %s given",
            ResponseInterface::class,
            get_class($actionResult)
        ));
    }
}