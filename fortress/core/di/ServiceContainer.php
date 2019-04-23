<?php

namespace fortress\core\di;

class ServiceContainer implements ContainerInterface {

    private $storage = [];

    private $objectCache = [];

    private $parameters = [];

    public function __construct() {
        $this->objectCache["container"] = $this;
    }

    public function set(string $name, $value) {
        if (is_object($value)) {
            $this->objectCache[$name] = $value;
        } else {
            $this->storage[$name] = $value;
        }
    }

    public function get(string $name) {
        if (array_key_exists($name, $this->objectCache)) {
            return $this->objectCache[$name];
        }

        if (array_key_exists($name, $this->storage)) {
            // TODO - резолвинг зависимостей инстанцируемого объекта!
            $namespace = $this->storage[$name];
            $obj = new $namespace();
            $this->objectCache[$name] = $obj;
            unset($this->storage[$name]);
            return $obj;
        }

        throw new DependencyNotFoundException($name);
    }

    public function getParameter(string $name) {
        if (array_key_exists($name)) {
            return $this->parameters[$name];
        }
        return null;
    }

    public function getParameterOrDefault(string $name, $default) {
        $parameter = $this->getParameter($name);
        return $parameter !== null ? $parameter : $default;
    }

    public function setParameter(string $name, $value) {
        $this->parameters[$name] = $value;
    }
}