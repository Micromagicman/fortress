<?php

namespace fortress\core\di;

use fortress\core\exception\FortressException;
use ReflectionClass;
use ReflectionException;

class Factory extends Resolver {

    public function build(string $className) {
        if (!class_exists($className)) {
            return null;
        }

        try {
            $reflectionObject = new ReflectionClass($className);
            $constructor = $reflectionObject->getConstructor();
            if (null == $constructor) {
                return $reflectionObject->newInstance();
            } else {
                $arguments = $this->resolveMethodArguments($constructor);
                return $reflectionObject->newInstanceArgs($arguments);
            }
        } catch (ReflectionException $e) {
            throw new FortressException("Object creation error", $e);
        }
    }


}