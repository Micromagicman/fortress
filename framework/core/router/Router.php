<?php

namespace fortress\core\router;

use fortress\core\exception\RouteNotFound;
use Symfony\Component\HttpFoundation\Request;

class Router {

    private $uriBuilder;

    private $routes;
    private $matchedRoute;

    public function __construct(
        UriBuilder $uriBuilder,
        RouteCollection $routeCollection
    ) {
        $this->routes = $routeCollection;
        $this->uriBuilder = $uriBuilder;
    }

    public function getRoute(string $name) {
        return $this->routes->getRouteByName($name);
    }

    public function getRouteCollection() {
        return $this->routes;
    }

    public function getMatchedRoute() {
        return $this->matchedRoute;
    }

    public function match(Request $request) {
        $requestUri = $request->getRequestUri();
        $uriChunks = $this->uriBuilder->buildUriChunks($requestUri);
        $method = $request->getMethod();
        foreach ($this->routes->all() as $name => $route) {
            if ($route->match($uriChunks, $method)) {
                $this->matchedRoute = $route;
            }
        }
        if (null == $this->matchedRoute) {
            throw new RouteNotFound($requestUri);
        }
        $variables = $this->extractUriVariables($uriChunks);
        $this->matchedRoute->setUriVariables($variables);
        return $this->matchedRoute;
    }

    public function buildUri(string $routeName, array $params = []) {
        $route = $this->routes->getRouteByName($routeName);
        if (null === $route) {
            return null;
        }
        return $this->uriBuilder->buildUri($route, $params);
    }

    private function extractUriVariables(array $uriChunks) {
        $uriIndex = 0;
        $variables = [];
        foreach ($this->matchedRoute->getChunks() as $key => $value) {
            if (is_string($key)) {
                $variables[$key] = $this->getVariableOfType($uriChunks[$uriIndex], $value);
            }
            $uriIndex++;
        }
        return $variables;
    }

    private function getVariableOfType($value, string $type) {
        return "int" === $type ? intval($value) : $value;
    }
}