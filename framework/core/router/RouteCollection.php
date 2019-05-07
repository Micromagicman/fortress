<?php

namespace fortress\core\router;

class RouteCollection {

    private $routes = [];

    public function addPrefix(string $prefix) {
        foreach ($this->routes as $name => $route) {
            $prevUri = $this->routes[$name]->getUri();
            $this->routes[$name]->setUri($prefix . $prevUri);
        }
    }

    public function addCollection(RouteCollection $collection) {
        foreach ($collection->all() as $name => $route) {
            $this->routes[$name] = $route;
        }
    }

    public function add(string $name, string $uri, array $route) {
        $this->routes[$name] = new Route(
            $name,
            $uri,
            $route["controller"],
            $route["action"],
            $route["methods"] ?? ["*"]
        );
    }

    public function all() {
        return $this->routes;
    }
}