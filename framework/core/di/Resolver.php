<?php

namespace fortress\core\di;

use Psr\Container\ContainerInterface;
use ReflectionMethod;

class Resolver {

    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    protected function resolveMethodArguments(ReflectionMethod $method) {
        $parameters = $method->getParameters();
        $arguments = [];
        foreach ($parameters as $p) {
            $cls = $p->getClass();
            if (null != $cls) {
                $arguments[] = $this->container->get($cls->getName());
            }
        }
        return $arguments;
    }
}