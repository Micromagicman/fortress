<?php

namespace fortress\core\di\holder;

use Closure;
use Psr\Container\ContainerInterface;

/**
 * Зависимость-фабрика
 * Фабрика необходима для lazy-loading-а некоторых тяжелых зависимостей,
 * которые будут построены по требованию
 * Class Service
 * @package fortress\core\di\holder
 */
class Factory implements DependencyHolder {

    private Closure $closure;

    private function __construct(Closure $callable) {
        $this->closure = $callable;
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerInterface $dependencyContainer) {
        return ($this->closure)($dependencyContainer);
    }

    public static function new(Closure $closure) {
        return new Factory($closure);
    }
}