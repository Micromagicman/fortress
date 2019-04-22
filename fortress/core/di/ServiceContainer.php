<?php

namespace fortress\core\di;

class ServiceContainer implements ContainerInterface {

    private $storage = [];

    private $cache = [];

    public function __construct() {
        $this->cache["container"] = $this;
    }

    public function set(string $name, $value) {
        $this->storage[$name] = $value;
    }

    public function get(string $name) {
        if (array_key_exists($name, $this->cache)) {
            return $this->cache[$name];
        }

        if (array_key_exists($name, $this->storage)) {
            // TODO - резолвинг зависимостей инстанцируемого объекта!
            $namespace = $this->storage[$name];
            $obj = new $namespace();
            $this->cache[$name] = $obj;
            unset($this->storage[$name]);
            return $obj;
        }

        throw new DependencyNotFoundException($name);
    }
}