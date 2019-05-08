<?php

namespace fortress\core\router;

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
        $uri = $request->getRequestUri();
        $method = $request->getMethod();

        foreach ($this->routes->all() as $name => $route) {
            if ($route->matches($uri, $method)) {
                $this->matchedRoute = $route;
            }
        }

        if (null == $this->matchedRoute) {
            throw new RouteNotFound($uri);
        }

        $variables = $this->extractUriVariables($uri);
        $this->matchedRoute->setUriVariables($variables);
        return $this->matchedRoute;
    }

    private function extractUriVariables(string $uri) {
        $variables = [];
        $uriChunks = explode("/", $uri);
        $routeUriChunks = explode("/", $this->matchedRoute->getUri());

        for ($i = 0; $i < count($uriChunks); $i++) {
            if ($routeUriChunks[$i] != $uriChunks[$i]) {
                $nameAndType = explode(":", trim($routeUriChunks[$i], "{}"));
                $variables[$nameAndType[0]] = $this->getVariableOfType($uriChunks[$i], $nameAndType[1]);
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