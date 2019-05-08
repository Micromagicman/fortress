<?php

namespace fortress\core\router;

class Route {

    private const VARIABLE_PATTERNS = [
        "/^\{[a-zA-Z]+:num\}$/" => "[0-9]+(\.[0-9]+)?", // Число
        "/^\{[a-zA-Z]+:str\}$/" => "[-.,;_a-zA-Zа-яА-Я]+" // Строка
    ];

    private $name;

    private $uri;

    private $uriRegex;

    private $requestMethods;

    private $controllerClass;

    private $actionName;

    private $uriVariables = [];

    public function __construct(
        string $name, 
        string $uri,
        string $controllerClass, 
        string $actionName, 
        array $requestMethods = ["*"]
    ) {
        $this->name = $name;
        $this->uri = $uri;
        $this->uriRegex = $this->createUrlRegex($uri);
        $this->controllerClass = $controllerClass;
        $this->actionName = $actionName;
        $this->requestMethods = array_map(function($m) {
            return mb_strtoupper($m);
        }, $requestMethods);
    }

    public function getUri() {
        return $this->uri;
    }

    public function setUri(string $uri) {
        $this->uri = $uri;
        $this->uriRegex = $this->createUrlRegex($uri);
    }

    public function getUriVariables() {
        return $this->uriVariables;
    }

    public function setUriVariables(array $variables) {
        $this->uriVariables = $variables;
    }

    public function getControllerClass() {
        return $this->controllerClass;
    }

    public function getActionName() {
        return $this->actionName;
    }

    public function isValidRequestMethod(string $method) {
        return $this->satisfiesAnyRequestMethod() || in_array($method, $this->requestMethods); 
    }

    public function satisfiesAnyRequestMethod() {
        return count($this->requestMethods) === 1 && $this->requestMethods[0] === "*";
    }

    public function matches(string $uri, string $method = "GET") {
        return $this->isValidRequestMethod($method) && preg_match($this->uriRegex, $uri);
    }

    private function createUrlRegex(string $uri) {
        $uriChunks = explode("/", $uri);
        $regexChunks = [];
        foreach ($uriChunks as $uriChunk) {
            $regexChunks[] = $this->replacePatternIfExists($uriChunk);
        }
        return "/^". implode("\\/", $regexChunks)  . "$/";
    }

    private function replacePatternIfExists(string $uriChunk) {
        foreach (self::VARIABLE_PATTERNS as $varPattern => $varRegex) {
            if (preg_match($varPattern, $uriChunk)) {
                return $varRegex;
            }
        }
        return $uriChunk;
    }
}