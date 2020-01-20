<?php

namespace fortress\core\router;

use Closure;
use fortress\cache\RouteCacheManager;
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
     * Загрузчик маршрутов, определенных в аннотациях
     * контроллера, и закэшированных в файл
     * @var RouteCacheManager
     */
    private RouteCacheManager $cacheManager;

    /**
     * Полный список маршрутов, определенных пользователем
     * @var RouteCollection
     */
    private RouteCollection $routeCollection;

    public function __construct(
        ContainerInterface $container,
        RouteCacheManager $cacheManager,
        RouteCollection $routeCollection
    ) {
        parent::__construct($container);
        $this->cacheManager = $cacheManager;
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
        /** @var Route $cachedRoute */
        foreach ($this->cacheManager->restore() as $cachedRoute) {
            $this->routeCollection->addRoute($cachedRoute->getName(), $cachedRoute);
        }
        return $next($request);
    }
}