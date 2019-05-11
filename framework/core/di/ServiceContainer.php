<?php

namespace fortress\core\di;

use Psr\Container\ContainerInterface;

class ServiceContainer implements ContainerInterface {

    private $factory;

    private $invoker;

    private $storage = [];

    private $objectCache = [];

    private $parameters = [];

    public function __construct() {
        $this->factory = new Factory($this);
        $this->invoker = new Invoker($this);
        // TODO - маппинг тип интерфейса -> конкретная реализация
        $this->objectCache[ContainerInterface::class] = $this;
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

    public function get($id) {
        if (array_key_exists($id, $this->objectCache)) {
            return $this->objectCache[$id];
        }

        if (array_key_exists($id, $this->storage)) {
            $obj = $this->build($this->storage[$id]);
            $this->objectCache[$id] = $obj;
            unset($this->storage[$id]);
            return $obj;
        }

        if (class_exists($id)) {
            $object = $this->getByClassName($id);
            if (null == $object) {
                $object = $this->build($id);
                $this->objectCache[$id] = $object;
            }
            return $object;
        }

        throw new DependencyNotFound($id);
    }

    public function has($id) {
        return array_key_exists($id, $this->objectCache) || array_key_exists($id, $this->storage);
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
        return $this->factory->build($className, $constructorArgs);
    }

    public function invoke($object, string $methodName, array $methodArgs = []) {
        // Если объект передан в качестве ключа в контейнере зависимостей
        if (is_string($object)) {
            $object = $this->get($object);
        }
        return $this->invoker->invoke($object, $methodName, $methodArgs);
    }

    /*
     * Получение объекта по имени класса (его неймспейсу)
     */
    private function getByClassName(string $className) {
        foreach ($this->objectCache as $name => $obj) {
            if ($obj instanceof $className) {
                return $obj;
            }
        }
        foreach ($this->storage as $name => $storageClassName) {
            if ($storageClassName === $className) {
                return $this->get($name);
            }
        }
        return null;
    }
}