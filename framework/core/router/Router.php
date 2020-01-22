<?php

namespace fortress\core\router;

use fortress\core\exception\RouteNotFound;
use fortress\core\exception\UriBuildException;

class Router {

    private UriBuilder $uriBuilder;

    private RouteCollection $routes;

    private Route $matchedRoute;

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

    public function getMatchedRoute() {
        return $this->matchedRoute;
    }

    /**
     * Поиск подходящего для заданного пути и HTTP-метода маршрута
     * @param string $uri
     * @param string $method
     * @return Route|mixed
     * @throws RouteNotFound
     */
    public function match(string $uri, string $method) {
        $uriChunks = $this->uriBuilder->buildUriChunks($uri);
        foreach ($this->routes->all() as $name => $route) {
            if ($route->match($uriChunks, $method)) {
                $this->matchedRoute = $route;
            }
        }
        if (!isset($this->matchedRoute)) {
            throw new RouteNotFound($method, $uri);
        }
        $variables = $this->extractUriVariables($uriChunks);
        $this->matchedRoute->setPathVariables($variables);
        return $this->matchedRoute;
    }

    /**
     * @param string $routeName
     * @param array $params
     * @return string|null
     * @throws UriBuildException
     */
    public function buildUri(string $routeName, array $params = []) {
        $route = $this->routes->getRouteByName($routeName);
        if (null === $route) {
            return null;
        }
        return $this->uriBuilder->buildPath($route, $params);
    }

    private function extractUriVariables(array $uriChunks) {
        $uriIndex = 0;
        $variables = [];
        foreach ($this->matchedRoute->getPathChunks() as $key => $value) {
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