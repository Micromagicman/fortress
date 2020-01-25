<?php

namespace fortress\core\configurator;

use Psr\Container\ContainerInterface;

class ParameterConfiguration extends Configuration {

    private const TEMPLATE_TYPE = "php";
    private const TEMPLATES_DIR = ".." . DIRECTORY_SEPARATOR . "templates";
    private const TEMPLATE_404_NAME = "404";

    public function initialize(ContainerInterface $container, ...$configs) {
        $container->setParameter("template.type", self::TEMPLATE_TYPE);
        $container->setParameter("template.404", self::TEMPLATE_404_NAME);
        $container->setParameter("template.dir", realpath(self::TEMPLATES_DIR));
        foreach ($configs as $config) {
            if ($config instanceof ConfigurationBag) {
                foreach ($this->flatConfiguration($config->items()) as $key => $value) {
                    $container->setParameter($key, $value);
                }
            }
        }
    }

    private function flatConfiguration(
        array $configuration,
        array &$resultConfiguration = [],
        string $prefix = ""
    ) {
        foreach ($configuration as $key => $value) {
            if (is_string($key) || is_numeric($key)) {
                if (is_array($value)) {
                    $this->flatConfiguration($value, $resultConfiguration, $prefix . $key . ".");
                } else {
                    $resultConfiguration[$prefix . $key] = "dir" === $key ? realpath($value) : $value;
                }
            }
        }
        return $resultConfiguration;
    }
}