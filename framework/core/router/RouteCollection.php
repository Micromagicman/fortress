<?php

namespace fortress\core\router;

class RouteCollection {

    private $routes = [];

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
        $this->routes[$name] = new Route(
            $name,
            $uriPattern,
            $routeConfiguration["controller"],
            $routeConfiguration["action"],
            $routeConfiguration["middleware"] ?? null,
            $routeConfiguration["methods"] ?? ["*"],
            $routeConfiguration["fuzzy"] ?? false
        );
    }

    public function get(string $name) {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    public function all() {
        return $this->routes;
    }
}