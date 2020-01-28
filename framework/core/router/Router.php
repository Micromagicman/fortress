<?php

namespace fortress\core\router;

use fortress\core\exception\UriBuildException;
use fortress\core\router\exception\RouteNotFound;

class Router {

    private UriBuilder $uriBuilder;

    private RouteCollection $routes;

    public function __construct(UriBuilder $uriBuilder, RouteCollection $routeCollection) {
        $this->routes = $routeCollection;
        $this->uriBuilder = $uriBuilder;
    }

    public function getRoute(string $name) {
        return $this->routes->getRouteByName($name);
    }

    public function getRouteCollection() {
        return $this->routes;
    }

    /**
     * Поиск подходящего для заданного пути и HTTP-метода маршрута
     * @param string $uri
     * @param string $method
     * @return Route|mixed
     * @throws RouteNotFound
     */
    public function match(string $uri, string $method) {
        $matchedRoute = null;
        $uriChunks = $this->uriBuilder->buildUriChunks($uri);
        foreach ($this->routes->all() as $name => $route) {
            if ($route->match($uriChunks, $method)) {
                $matchedRoute = $route;
            }
        }
        if (!isset($matchedRoute)) {
            throw new RouteNotFound($method, $uri);
        }
        $variables = $this->extractUriVariables($matchedRoute, $uriChunks);
        $matchedRoute->setPathVariables($variables);
        return $matchedRoute;
    }

    /**
     * @param string $routeName
     * @param array $params
     * @return string|null
     * @throws UriBuildException
     */
    public function buildPath(string $routeName, array $params = []) {
        $route = $this->routes->getRouteByName($routeName);
        if (null === $route) {
            return null;
        }
        return $this->uriBuilder->buildPath($route, $params);
    }

    private function extractUriVariables(Route $matchedRoute, array $uriChunks) {
        $uriIndex = 0;
        $variables = [];
        foreach ($matchedRoute->getPathChunks() as $key => $value) {
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