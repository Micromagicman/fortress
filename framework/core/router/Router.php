<?php

namespace fortress\core\router;

use fortress\core\exception\RouteNotFound;
use Symfony\Component\HttpFoundation\Request;

class Router {

    private $routes;

    private $matchedRoute;

    public function __construct() {
        $this->routes = new RouteCollection();
    }

    public function getRouteCollection() {
        return $this->routes;
    }

    public function getMatchedRoute() {
        return $this->matchedRoute;
    }

    public function match(Request $request) {
        $requestUri = $request->getRequestUri();
        $purifiedUri = strtok($requestUri, "?");
        $method = $request->getMethod();

        foreach ($this->routes->all() as $name => $route) {
            if ($route->matches($purifiedUri, $method)) {
                $this->matchedRoute = $route;
            }
        }

        if (null == $this->matchedRoute) {
            throw new RouteNotFound($requestUri);
        }

        $variables = $this->extractUriVariables($purifiedUri);
        $this->matchedRoute->setUriVariables($variables);
        return $this->matchedRoute;
    }

    private function extractUriVariables(string $uri) {
        $variables = [];
        $uriChunks = explode("/", $uri);
        $routeUriChunks = explode("/", $this->matchedRoute->getUri());

        for ($i = 0; $i < count($routeUriChunks); $i++) {
            if ("*" != $routeUriChunks[$i]) {
                // Извлечение переменной из URI
                if ($routeUriChunks[$i] != $uriChunks[$i]) {
                    $nameAndType = explode(":", trim($routeUriChunks[$i], "{}"));
                    $variables[$nameAndType[0]] = $this->getVariableOfType($uriChunks[$i], $nameAndType[1]);
                }
            }
        }

        return $variables;
    }

    private function getVariableOfType($value, string $type) {
        if ("num" == $type) {
            return mb_strpos($value, ".") ? floatval($value) : intval($value);
        }
        return $value;
    }
}