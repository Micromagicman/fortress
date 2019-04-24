<?php

namespace fortress\core\router;

class RouteCollection {

    private $routes = [];

    public function addCollection(RouteCollection $collection) {
        foreach ($collection->all() as $name => $route) {
            $this->routes[$name] = $route;
        }
    }

    public function add(string $name, string $url, array $route) {
        // $urlRegex = Route::createUrlRegex($url);
        $this->routes[$name] = new Route(
            $name,
            $url,
            $route["controller"],
            $route["action"],
            $route["method"] ?? ["*"]
        );
    }

    public function all() {
        return $this->routes;
    }
}