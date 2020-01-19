<?php

namespace fortress\core\router;

use Closure;
use fortress\core\configuration\Configuration;
use fortress\core\configuration\ConfigurationNotFoundException;
use fortress\core\middleware\BeforeAction;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Загрзузчик маршрутов из конфигурационного файла и кэша
 * Class RouterInitializer
 * @package fortress\core\router
 */
class RouterInitializer extends BeforeAction {

    /**
     * Полный список маршрутов, определенных пользователем
     * @var RouteCollection
     */
    private RouteCollection $routeCollection;

    public function __construct(
        ContainerInterface $container,
        RouteCollection $routeCollection
    ) {
        parent::__construct($container);
        $this->routeCollection = $routeCollection;
    }

    /**
     * Загрузка маршрутов
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     * @throws ConfigurationNotFoundException
     */
    protected function handleRequest(ServerRequestInterface $request, callable $next) {
        $routes = Configuration::loadConfiguration(Configuration::ROUTES_CONFIGURATION_NAME);
        if ($routes instanceof Closure) {
            $routes($this->routeCollection);
        } else if (is_array($routes)) {
            foreach ($routes as $routeInitializer) {
                if ($routeInitializer instanceof Closure) {
                    $routeInitializer($this->routeCollection);
                }
            }
        }
        return $next($request);
    }
}