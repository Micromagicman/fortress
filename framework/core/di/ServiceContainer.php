<?php

namespace fortress\core\di;

class ServiceContainer implements ContainerInterface {

    private $builder;

    private $invoker;

    private $storage = [];

    private $objectCache = [];

    private $parameters = [];

    public function __construct() {
        $this->builder = new ObjectBuilder();
        $this->invoker = new MethodInvoker();
        $this->objectCache["container"] = $this;
    }

    public function set(string $name, $value) {
        if (is_object($value)) {
            $this->objectCache[$name] = $value;
            unset($this->storage[$name]);
        } else {
            $this->storage[$name] = $value;
            unset($this->objectCache[$name]);
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
        return $this->getParameterOrDefault($name, null);
    }

    public function getParameterOrDefault(string $name, $default) {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }
        return $default;
    }

    public function setParameter(string $name, $value) {
        $this->parameters[$name] = $value;
    }

    public function build(string $className, array $constructorArgs = []) {
        return $this->builder->build($className, $constructorArgs);
    }

    public function invoke($object, string $methodName, array $methodArgs = []) {
        // Если объект передан в качестве ключа в контейнере зависимостей
        if (is_string($object)) {
            $object = $this->get($object);
        }
        return $this->invoker->invoke($object, $methodName, $methodArgs);
    }
}