<?php

namespace fortress\core\router;

use fortress\core\exception\RouteException;

class Route {

    private static $URI_PARAMETER_PATTERNS = [
        "int" => "/[0-9]+(\.[0-9]+)?/", // Число
        "str" => "/[-.,;_a-zA-Zа-яА-Я]+/" // Строка
    ];

    private $name;

    private $uriPattern;
    private $requestMethods = [];
    private $chunks = [];
    private $uriVariables = [];
    private $fuzzy;

    private $controllerClass;
    private $actionName;
    private $middlewareClass;

    public function __construct(
        string $name,
        string $uriPattern,
        string $controllerClass,
        string $actionName,
        string $middlewareClass = null,
        array $requestMethods = ["*"],
        bool $fuzzy = false
    ) {
        $this->name = $name;
        $this->uriPattern = $uriPattern;
        $this->fuzzy = $fuzzy;
        $this->controllerClass = $controllerClass;
        $this->actionName = $actionName;
        $this->middlewareClass = $middlewareClass;
        $this->refreshChunks($uriPattern);
        $this->requestMethods = array_map(function ($m) {
            return mb_strtoupper($m);
        }, $requestMethods);
    }

    public function getUriPattern() {
        return $this->uriPattern;
    }

    public function setUriPattern(string $uriPattern) {
        $this->uriPattern = $uriPattern;
        $this->refreshChunks($uriPattern);
    }

    public function getChunks() {
        return $this->chunks;
    }

    public function getUriVariables() {
        return $this->uriVariables;
    }

    public function setUriVariables(array $uriVariables = []) {
        $this->uriVariables = $uriVariables;
    }

    public function getControllerClass() {
        return $this->controllerClass;
    }

    public function getActionName() {
        return $this->actionName;
    }

    public function getMiddlewareClass() {
        return $this->middlewareClass;
    }

    public function setMiddlewareClass(string $middlewareClass) {
        $this->middlewareClass = $middlewareClass;
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
        if (count($uriChunks) < count($this->chunks)) {
            return false;
        }
        if (!$this->fuzzy && count($uriChunks) !== count($this->chunks)) {
            return false;
        }
        foreach ($this->chunks as $key => $value) {
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

    private function refreshChunks(string $uri) {
        $this->chunks = [];
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
                $this->chunks[$uriParameter[0]] = $uriParameter[1];
            } else {
                $this->chunks[] = $chunk;
            }
        }
    }
}