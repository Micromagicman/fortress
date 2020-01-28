<?php

namespace fortress\core\configuration;

use fortress\util\collection\ArrayUtils;
use fortress\util\common\StringUtils;

class Configuration {

    public const DEFAULT_CONFIGURATION_DIR = "config";
    public const DEFAULT_CONFIGURATION_EXTENSION = ".php";
    public const DEFAULT_TEMPLATES_DIR = "templates";
    public const DEFAULT_TEMPLATE_TYPE = "php";
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

    /**
     * Конфигурационные параметры подсистемы безопасности
     */
    public const CSRF_TOKEN_KEY = "CSRF_TOKEN";

    private string $configurationDir;

    private string $templatesDir;

    private array $configNamesCache = [];

    public function __construct(
        string $bootstrapFilePath,
        string $configurationDir = self::DEFAULT_CONFIGURATION_DIR,
        string $templatesDir = self::DEFAULT_TEMPLATES_DIR) {
        if (StringUtils::isEmpty($configurationDir)) {
            $configurationDir = self::DEFAULT_CONFIGURATION_DIR;
        }
        if (StringUtils::isEmpty($templatesDir)) {
            $templatesDir = self::DEFAULT_TEMPLATES_DIR;
        }
        $this->configurationDir = $bootstrapFilePath . DIRECTORY_SEPARATOR . $configurationDir;
        $this->templatesDir = $bootstrapFilePath . DIRECTORY_SEPARATOR . $templatesDir;
    }

    public function getTemplatesDir() {
        return $this->templatesDir;
    }

    public function getConfigFilePath(string $fileName) {
        if (!StringUtils::endsWith($fileName, self::DEFAULT_CONFIGURATION_EXTENSION)) {
            $fileName .= self::DEFAULT_CONFIGURATION_EXTENSION;
        }
        return ArrayUtils::computeIfNotPresent($this->configNamesCache, $fileName, function ($key) {
            return $this->configurationDir . DIRECTORY_SEPARATOR . $key;
        });
    }

    public function isConfigurationExists(string $fileName) {
        return file_exists($this->getConfigFilePath($fileName));
    }

    /**
     * @param string $fileName
     * @return mixed
     * @throws ConfigurationNotFoundException
     */
    public function loadConfiguration(string $fileName) {
        $configFilePath = $this->getConfigFilePath($fileName);
        if (!$this->isConfigurationExists($fileName)) {
            throw new ConfigurationNotFoundException($configFilePath);
        }
        return require($configFilePath);
    }

    /**
     * Загрузка параметров из конфигурационных файлов
     * @return array
     */
    public function configure() {
        $configurations = [];
        foreach (self::getConfigurations() as $configFile) {
            try {
                $configurations[] = $this->loadConfiguration($configFile);
            } catch (ConfigurationNotFoundException $e) {}
        }
        return $configurations;
    }

    private static function getConfigurations() {
        return [
            self::DATABASE_CONFIGURATION_NAME,
            self::PARAMETERS_CONFIGURATION_NAME,
            self::SECURITY_CONFIGURATION_NAME,
            self::SERVICE_CONFIGURATION_NAME
        ];
    }
}