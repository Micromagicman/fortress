<?php

namespace fortress\core\router;

use fortress\util\common\StringUtils;
use InvalidArgumentException;

/**
 * Class RouteCollection
 * @package fortress\core\router
 * Список маршрутов веб приложения
 */
class RouteCollection {

    /**
     * HTTP методы, доступные для маршрутизации
     */
    public const AVAILABLE_METHODS = [
        "GET",
        "POST",
        "PUT",
        "DELETE",
        "PATCH"
    ];

    /**
     *  Список маршрутов
     */
    private array $routes = [];

    /**
     * @param string $prefix
     * Добавление префикса ко всем маршрутам
     * Например, маршрут имеет путь /article/list
     * При добавлении префикса /api, данный маршрут будет
     * обрабатывать путь /api/article/list
     */
    public function addPrefix(string $prefix) {
        if (!StringUtils::startsWith($prefix, UriBuilder::URI_SEPARATOR)) {
            $prefix = UriBuilder::URI_SEPARATOR . $prefix;
        }
        foreach ($this->routes as $route) {
            $prevUri = $route->getUriPattern();
            $route->setUriPattern($prefix . $prevUri);
        }
    }

    public function addBeforeAction(string $middlewareClass) {
        /** @var Route $route */
        foreach ($this->routes as $route) {
            $route->addBeforeActions($middlewareClass);
        }
    }

    public function addAfterAction(string $middlewareClass) {
        /** @var Route $route */
        foreach ($this->routes as $route) {
            $route->addAfterActions($middlewareClass);
        }
    }

    public function addCollection(RouteCollection $collection) {
        foreach ($collection->all() as $name => $route) {
            $this->routes[$name] = $route;
        }
    }

    public function add(string $name, string $uriPattern, array $routeConfiguration) {
        return $this->addRoute($name, new Route(
            $name,
            $uriPattern,
            $routeConfiguration["controller"],
            $routeConfiguration["methods"] ?? ["*"],
            $routeConfiguration["beforeActions"] ?? [],
            $routeConfiguration["afterActions"] ?? [],
            $routeConfiguration["fuzzy"] ?? false
        ));
    }

    public function addRoute(string $name, Route $route) {
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
        if (!in_array($nameUpper, self::AVAILABLE_METHODS)) {
            throw new InvalidArgumentException("Method '$nameUpper' is not allowed!");
        }
        if (count($arguments) < 3) {
            throw new InvalidArgumentException("You must provide routeName, routePattern, and routeClass parameters");
        }
        $controller = $arguments[2];
        if (is_string($controller)) {
            return $this->add(
                $arguments[0], // Route name
                $arguments[1], // Route uriPattern
                [
                    "controller" => $controller,
                    "beforeActions" => $arguments[3] ?? [],
                    "afterActions" => $arguments[4] ?? [],
                    "methods" => [$nameUpper]
                ]
            );
        }
        return null;
    }
}