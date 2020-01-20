<?php

namespace fortress\core\configurator;

use fortress\core\database\DatabaseConfiguration;
use fortress\core\router\RouteCollection;
use fortress\core\router\Router;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Configurator {

    private const CONFIG_DIR = ".." . DIRECTORY_SEPARATOR . "config";

    public function initializeContainer(ContainerInterface $container, Request $request) {
        // parameters
        $this->initializeParameters($container, new ParameterConfiguration());
        // core services
        $this->initializeRouter($container);
        $this->initializeDatabase($container);
        // custom services
        $this->initializeServices($container);
        // security module
        $this->initializeSecurity($container, new SecurityConfiguration());
        // request object
        $container->set(Request::class, $request);
    }

    private function initializeParameters(
        ContainerInterface $container,
        ParameterConfiguration $parameterConfiguration
    ) {
        $customConfiguration = $this->tryToLoadConfigurationOrDefault(
            "parameters",
            ConfigurationBag::empty()
        );
        $parameterConfiguration->initialize($container, $customConfiguration);
    }

    private function initializeServices(ContainerInterface $container) {
        $serviceInitializer = $this->tryToLoadConfigurationOrDefault("services");
        if (is_callable($serviceInitializer)) {
            $serviceInitializer($container);
        }
    }

    private function initializeRouter(ContainerInterface $container) {
        $initializer = $this->tryToLoadConfigurationOrDefault("routes", ConfigurationBag::empty());
        $routeCollection = $container->get(Router::class)->getRouteCollection();

        if ($initializer instanceof ConfigurationBag) {
            foreach ($initializer->items() as $routeInitializer) {
                if (is_callable($routeInitializer)) {
                    $collection = new RouteCollection();
                    $routeInitializer($collection);
                    $routeCollection->addCollection($collection);
                }
            }
        } else if (is_callable($initializer)) {
            $initializer($routeCollection);
        }
    }

    private function initializeDatabase(ContainerInterface $container) {
        $config = $this->tryToLoadConfigurationOrDefault("database", ConfigurationBag::empty());
        $container->set(DatabaseConfiguration::class, DatabaseConfiguration::build($config));
    }

    private function initializeSecurity(ContainerInterface $container, SecurityConfiguration $securityConfiguration) {
        $securityConfig = $this->tryToLoadConfigurationOrDefault("security", ConfigurationBag::empty());
        $securityConfiguration->initialize($container, $securityConfig);
    }

    private function tryToLoadConfigurationOrDefault(string $configName, $default = null) {
        $configPath = self::CONFIG_DIR . DIRECTORY_SEPARATOR . $configName . ".php";
        if (file_exists($configPath)) {
            $config = require_once($configPath);
            if (is_array($config)) {
                return new ConfigurationBag($config);
            }
            return $config;
        }
        return $default;
    }
}