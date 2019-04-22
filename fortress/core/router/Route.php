<?php

namespace fortress\core\router;

use fortress\core\di\ContainerInterface;

class Route {
    
    private $name;

    private $urlRegex;

    private $requestMethods;

    private $controllerClass;

    private $controllerMethod;

    public function __construct(
        string $name, 
        string $urlRegex,
        string $controllerClass, 
        string $controllerMethod, 
        array $requestMethods = ["*"]
    ) {
        $this->name = $name;
        $this->urlRegex = $urlRegex;
        $this->controllerClass = $controllerClass;
        $this->controllerMethod = $controllerMethod;
        $this->requestMethods = array_map(function($m) {
            return mb_strtoupper($m);
        }, $requestMethods);
    }

    public function getName() {
        return $this->name;
    }

    public function isValidRequestMethod(string $method) {
        return $this->satisfiesAnyRequestMethod() || in_array($method, $this->requestMethods); 
    }

    public function satisfiesAnyRequestMethod() {
        return count($this->requestMethods) === 1 && $this->requestMethods[0] === "*";
    }
}