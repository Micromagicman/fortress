<?php

namespace fortress\core\di;

class MethodInvoker {
    
    public function invoke($object, string $methodName, array $methodArgs = []) {
        if (!method_exists($object, $methodName)) {
            throw new MethodNotExistException($methodName);
        }
        return call_user_func([$object, $methodName], $methodArgs);
    }
}