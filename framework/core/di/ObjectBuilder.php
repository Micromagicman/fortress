<?php

namespace fortress\core\di;

class ObjectBuilder {

    public function build(string $className, array $constructorArgs = []) {
        if (!class_exists($className)) {
            return null;
        }

        $reflectionObject = new \ReflectionClass($className);
        $constructor = $reflectionObject->getConstructor();

        try {
            return $reflectionObject->newInstanceArgs($constructorArgs);
        } catch (\ReflectionException $e) {
            return null;
        }
    }
}