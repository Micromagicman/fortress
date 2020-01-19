<?php

namespace fortress\core\configuration;

use Exception;

class ConfigurationNotFoundException extends Exception {

    private string $configurationFileName;

    public function __construct($configurationFileName) {
        parent::__construct("Configuration file '$configurationFileName' not found");
    }

    public function getConfigurationFileName() {
        return $this->configurationFileName;
    }
}