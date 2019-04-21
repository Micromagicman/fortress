<?php

namespace fortress\core;

use fortress\core\configurator\Configurator;
use fortress\core\di\ContainerInterface;
use fortress\core\di\DependencyNotFoundException;

use Symfony\Component\HttpFoundation\Request;

class Framework implements ContainerInterface {

    private $storage = [];

    private $dependencyCache = [];

    public function __construct(Configurator $conf) {
        $conf->initializeContainer($this);
    }

    public function set(string $name, $value) {
        $this->storage[$name] = $value;
    }

    public function get(string $name) {
        if (array_key_exists($name, $this->dependencyCache)) {
            return $this->dependencyCache[$name];
        }

        if (array_key_exists($name, $this->storage)) {
            // TODO - резолвинг зависимостей инстанцируемого объекта!
            $namespace = $this->storage[$name];
            $obj = new $namespace();
            $this->dependencyCache[$name] = $obj;
            return $obj;
        }

        throw new DependencyNotFoundException($name);
    }

    public function run(Request $request) {
    }
}
