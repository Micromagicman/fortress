<?php

namespace fortress\core\router;

use Symfony\Component\HttpFoundation\Request;

class Router {

    private $routes;

    public function __construct() {
        $this->routes = new RouteCollection();
    }

    public function getRouteCollection() {
        return $this->routes;
    }

    public function match(Request $request) {
        $url = $request->getBaseUrl();
        foreach ($this->routes->all() as $name => $route) {
            if ($route->match($url)) {
                return $route;
            }
        }
        throw new RouteNotFoundException($url);
    }
}