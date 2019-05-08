<?php

namespace fortress\core\di;

use ReflectionException;
use ReflectionMethod;

class Invoker extends Resolver {

    public function invoke($object, string $methodName, array $methodArgs = []) {
        try {
            $method = new ReflectionMethod($object, $methodName);
            $arguments = $this->resolveMethodArguments($method);
            $arguments = array_merge($methodArgs, $arguments);
            return $method->invokeArgs($object, $arguments);
        } catch (ReflectionException $e) {
            return null;
        }
    }
}