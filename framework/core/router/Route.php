<?php

namespace fortress\core\router;

use fortress\core\exception\RouteException;

class Route {

    /**
     * Регулярки для матчинга path-variables
     * @var array
     */
    private static array $URI_PARAMETER_PATTERNS = [
        "int" => "/[0-9]+(\.[0-9]+)?/", // Число
        "str" => "/[-.,;_a-zA-Zа-яА-Я]+/" // Строка
    ];

    /**
     * Уникальное имя маршрута
     * @var string
     */
    private string $name;

    /**
     * Регулярка, которой должен соответствовать uri path
     * @var string
     */
    private string $pathPattern;

    /**
     * HTTP-методы, которым удовлетворяет данный маршрут
     * @var array
     */
    private array $requestMethods = [];

    /**
     * Части path (Пример: path = /api/1/test -> chunks = {api, 1, test})
     * @var array
     */
    private array $pathChunks = [];

    /**
     * Переменные, определенные в пути
     * @var array
     */
    private array $pathVariables = [];

    /**
     * Класс контроллера, который соответсвует данному маршруту
     * @var string
     */
    private string $controllerClass;

    /**
     * Класс промежуточного кода, который соответсвует данному маршруту
     * @var array
     */
    private array $beforeActions = [];

    /**
     * Класс промежуточного кода, который соответсвует данному маршруту
     * @var array
     */
    private array $afterActions = [];

    /**
     * Является ли данный маршрут "расширяемым".
     * Если маршрут расширяемый, то он соответсвует всем путям, для
     * которых {@var $pathPattern} является префиксом
     * Например - паттерн /app будет соответствовать маршрутам
     * /app/test
     * /app/article
     * /app/some/other/path и т.д
     * @var bool
     */
    private bool $fuzzy;

    public function __construct(
        string $name,
        string $uriPattern,
        string $controllerClass,
        array $requestMethods = ["*"],
        array $beforeActions = [],
        array $afterActions = [],
        bool $fuzzy = false
    ) {
        $this->name = $name;
        $this->pathPattern = $uriPattern;
        $this->controllerClass = $controllerClass;
        $this->afterActions = $afterActions;
        $this->beforeActions = $beforeActions;
        $this->fuzzy = $fuzzy;
        $this->requestMethods = $this->prepareRequestMethods($requestMethods);
        $this->refreshChunks($uriPattern);
    }

    public function getName() {
        return $this->name;
    }

    public function getPathPattern() {
        return $this->pathPattern;
    }

    public function setPathPattern(string $pathPattern) {
        $this->pathPattern = $pathPattern;
        $this->refreshChunks($pathPattern);
    }

    public function getPathChunks() {
        return $this->pathChunks;
    }

    public function getPathVariables() {
        return $this->pathVariables;
    }

    public function setPathVariables(array $pathVariables = []) {
        $this->pathVariables = $pathVariables;
    }

    public function getControllerClass() {
        return $this->controllerClass;
    }

    public function getBeforeActions() {
        return $this->beforeActions;
    }

    public function addBeforeActions(string... $actions) {
        foreach ($actions as $action) {
            $this->beforeActions[] = $action;
        }
    }

    public function getAfterActions() {
        return $this->afterActions;
    }

    public function addAfterActions(string... $actions) {
        foreach ($actions as $action) {
            $this->afterActions[] = $action;
        }
    }

    public function isValidRequestMethod(string $method) {
        return $this->satisfiesAnyRequestMethod() || in_array($method, $this->requestMethods);
    }

    public function satisfiesAnyRequestMethod() {
        return count($this->requestMethods) === 1 && $this->requestMethods[0] === "*";
    }

    public function match(array $uriChunks, string $method = "GET") {
        if (!$this->isValidRequestMethod($method)) {
            return false;
        }
        $uriIndex = 0;
        if (count($uriChunks) < count($this->pathChunks)) {
            return false;
        }
        if (!$this->fuzzy && count($uriChunks) !== count($this->pathChunks)) {
            return false;
        }
        foreach ($this->pathChunks as $key => $value) {
            // Проверка статичных частей роута
            if (is_int($key) && $value !== $uriChunks[$uriIndex]) {
                return false;
            }
            // Проверка параметров роутера
            if (is_string($key) && !preg_match(static::$URI_PARAMETER_PATTERNS[$value], $uriChunks[$uriIndex])) {
                return false;
            }
            $uriIndex++;
        }
        return true;
    }

    /**
     * Подготовка переданных HTTP-методов для корректного матчинга
     * @param array $methods
     * @return array
     */
    private function prepareRequestMethods(array $methods) {
        $preparedMethods = [];
        foreach ($methods as $method) {
            $preparedMethods[] = mb_strtoupper($method);
        }
        return $preparedMethods;
    }

    private function refreshChunks(string $uri) {
        $this->pathChunks = [];
        $uriChunks = explode("/", trim($uri, "/"));
        foreach ($uriChunks as $chunk) {
            if (empty($chunk)) {
                continue;
            }
            // Извлечение имени и типа параметра роута
            if ("{" === $chunk[0] && "}" === $chunk[mb_strlen($chunk) - 1]) {
                $uriParameterPattern = trim($chunk, "{}");
                $uriParameter = explode(":", $uriParameterPattern);
                if (count($uriParameter) < 2 || !array_key_exists($uriParameter[1], static::$URI_PARAMETER_PATTERNS)) {
                    throw new RouteException("Uri parameter pattern should look like {name:type}");
                }
                $this->pathChunks[$uriParameter[0]] = $uriParameter[1];
            } else {
                $this->pathChunks[] = $chunk;
            }
        }
    }
}