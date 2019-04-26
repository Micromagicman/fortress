<?php

namespace fortress\core\router;

use fortress\core\di\ContainerInterface;

class Route {

    private const VARIABLE_PATTERNS = [
        "/^\{[a-zA-Z]+:num\}$/" => "[0-9]+(\.[0-9]+)?", // Число
        "/^\{[a-zA-Z]+:str\}$/" => "[-.,;_a-zA-Zа-яА-Я]+" // Строка
    ];

    private $name;

    private $urlRegex;

    private $requestMethods;

    private $controllerClass;

    private $actionName;

    private $variables = [];

    public function __construct(
        string $name, 
        string $urlRegex,
        string $controllerClass, 
        string $actionName, 
        array $requestMethods = ["*"]
    ) {
        $this->name = $name;
        $this->urlRegex = $this->createUrlRegex($urlRegex);
        $this->controllerClass = $controllerClass;
        $this->actionName = $actionName;
        $this->requestMethods = array_map(function($m) {
            return mb_strtoupper($m);
        }, $requestMethods);
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

    public function matches($url, $method = "GET") {
        return $this->isValidRequestMethod($method) && preg_match($this->urlRegex, $url);
    }

    public function getVariables(string $url) {
        // TODO - извлечение переменных из url
    }

    private function createUrlRegex(string $url) {
        $urlChunks = explode("/", $url);
        $regexChunks = [];
        foreach ($urlChunks as $urlChunk) {
            $regexChunks[] = $this->replacePatternIfExists($urlChunk);
        }
        return "/^". implode("\\/", $regexChunks)  . "$/";
    }

    private function replacePatternIfExists(string $urlChunk) {
        foreach (self::VARIABLE_PATTERNS as $varPattern => $varRegex) {
            if (preg_match($varPattern, $urlChunk)) {
                return $varRegex;
            }
        }
        return $urlChunk;
    }
}