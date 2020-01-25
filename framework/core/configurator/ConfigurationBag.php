<?php

namespace fortress\core\configurator;

class ConfigurationBag {

    private $items;

    public function __construct(array $config) {
        $this->items = $config;
    }

    public function get(string $key, $default = null) {
        if (!isset($this->items[$key])) {
            return $default;
        }
        return is_array($this->items[$key])
            ? new ConfigurationBag($this->items[$key])
            : $this->items[$key];
    }

    public function items() {
        return $this->items;
    }

    public static function empty() {
        return new ConfigurationBag([]);
    }
}