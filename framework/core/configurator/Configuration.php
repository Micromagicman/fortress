<?php

namespace fortress\core\configurator;

use Psr\Container\ContainerInterface;

abstract class Configuration {

    public abstract function initialize(ContainerInterface $container, ...$params);
}