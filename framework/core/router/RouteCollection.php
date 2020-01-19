<?php

namespace fortress\core\router;

use InvalidArgumentException;

class RouteCollection {

    private array $routes = [];

    public function addPrefix(string $prefix) {
        foreach ($this->routes as $route) {
            $prevUri = $route->getUriPattern();
            $route->setUriPattern($prefix . $prevUri);
        }
    }

    public function addMiddleware(string $middlewareClass) {
        foreach ($this->routes as $route) {
            $route->setMiddlewareClass($middlewareClass);
        }
    }

    public function addCollection(RouteCollection $collection) {
        foreach ($collection->all() as $name => $route) {
            $this->routes[$name] = $route;
        }
    }

    public function add(string $name, string $uriPattern, array $routeConfiguration) {
        $route = new Route(
            $name,
            $uriPattern,
            $routeConfiguration["controller"],
            $routeConfiguration["middleware"] ?? "",
            $routeConfiguration["methods"] ?? ["*"],
            $routeConfiguration["fuzzy"] ?? false
        );
        $this->routes[$name] = $route;
        return $route;
    }

    public function getRouteByName(string $name) {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    public function all() {
        return $this->routes;
    }

    public function __call($name, $arguments) {
        $nameUpper = strtoupper($name);
        if (!in_array($nameUpper, ["GET", "POST", "PUT", "DELETE", "PATCH"])) {
            throw new InvalidArgumentException("Method '$nameUpper' is not allowed!");
        }
        if (count($arguments) < 3) {
            throw new InvalidArgumentException("Method '$nameUpper' is not allowed!");
        }
        $controller = $arguments[2];
        if (is_string($controller)) {
            return $this->add(
                $arguments[0], // Route name
                $arguments[1], // Route uriPattern
                [
                    "controller" => $controller,
                    "methods" => [$nameUpper]
                ]
            );
        }
        return null;
    }
}