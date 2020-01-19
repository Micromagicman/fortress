<?php

namespace fortress\core\configuration;

use fortress\core\di\loader\MapLoader;
use fortress\core\router\RouteCollection;
use fortress\util\common\StringUtils;
use Psr\Http\Message\ServerRequestInterface;

class Configuration {

    public const CONFIGURATION_DIR = ".." . DIRECTORY_SEPARATOR . "config";
    public const DEFAULT_CONFIGURATION_EXTENSION = ".php";

    /**
     * Имена конфигурационных файлов
     */
    public const DATABASE_CONFIGURATION_NAME = "database";
    public const PARAMETERS_CONFIGURATION_NAME = "parameters";
    public const SERVICE_CONFIGURATION_NAME = "services";
    public const ROUTES_CONFIGURATION_NAME = "routes";
    public const SECURITY_CONFIGURATION_NAME = "security";

    /**
     * Конфигурационные параметры базы данных
     */
    public const DATABASE_NAME_KEY = "DB_NAME";
    public const DATABASE_HOST_KEY = "DB_HOST";
    public const DATABASE_PORT_KEY = "DB_PORT";
    public const DATABASE_DRIVER_KEY = "DB_DRIVER";
    public const DATABASE_USERNAME_KEY = "DB_USERNAME";
    public const DATABASE_PASSWORD_KEY = "DB_PASSWORD";

    private static array $configNamesCache = [];

    public static function getConfigFilePath(string $fileName) {
        if (!StringUtils::endsWith($fileName, self::DEFAULT_CONFIGURATION_EXTENSION)) {
            $fileName .= self::DEFAULT_CONFIGURATION_EXTENSION;
        }
        if (array_key_exists($fileName, static::$configNamesCache)) {
            return static::$configNamesCache[$fileName];
        }
        $configPath = self::CONFIGURATION_DIR . DIRECTORY_SEPARATOR . $fileName;
        static::$configNamesCache[$fileName] = $configPath;
        return $configPath;
    }

    public static function isConfigurationExists(string $fileName) {
        return file_exists(self::getConfigFilePath($fileName));
    }

    /**
     * @param string $fileName
     * @return mixed
     * @throws ConfigurationNotFoundException
     */
    public static function loadConfiguration(string $fileName) {
        $configFilePath = self::getConfigFilePath($fileName);
        if (!self::isConfigurationExists($fileName)) {
            throw new ConfigurationNotFoundException($configFilePath);
        }
        return require_once($configFilePath);
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    public static function configure(ServerRequestInterface $request) {
        $configurations = [];
        foreach ([
                     self::DATABASE_CONFIGURATION_NAME,
                     self::PARAMETERS_CONFIGURATION_NAME,
                     self::SECURITY_CONFIGURATION_NAME,
                     self::SERVICE_CONFIGURATION_NAME
                 ] as $configFile) {
            try {
                $configurations[] = self::loadConfiguration($configFile);
            } catch (ConfigurationNotFoundException $e) {}
        }
        $configurations[] = new MapLoader([
            ServerRequestInterface::class => $request,
            RouteCollection::class => self::configureRoutes()
        ]);
        return $configurations;
    }

    public static function configureRoutes() {
        $routeCollection = new RouteCollection();
        try {
            $routes = self::loadConfiguration(self::ROUTES_CONFIGURATION_NAME);
            if (is_array($routes)) {
                foreach ($routes as $routeInitializer) {
                    if (is_callable($routeInitializer)) {
                        $routeInitializer($routeCollection);
                    }
                }
            } else if (is_callable($routes)) {
                $routes($routeCollection);
            }
        } catch (ConfigurationNotFoundException $e) {}
        return $routeCollection;
    }
}