<?php

namespace fortress\core\di;

use fortress\core\exception\FortressException;
use ReflectionClass;
use ReflectionException;

class Factory extends Resolver {

    public function build(string $className) {
        try {
            if (!class_exists($className)) {
                throw new FortressException("Class '$className' not found");
            }
            $reflectionObject = new ReflectionClass($className);
            $constructor = $reflectionObject->getConstructor();
            if (null === $constructor) {
                return $reflectionObject->newInstance();
            }
            $arguments = $this->resolveMethodArguments($constructor);
            return $reflectionObject->newInstanceArgs($arguments);
        } catch (ReflectionException $e) {
            throw new FortressException("Object creation error", $e);
        } catch (FortressException $e) {
            throw $e;
        }
    }
}