<?php

namespace fortress\core\di\holder;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;

/**
 * Зависимость-значение
 * Это простейшие зависимости, которые не нужно разрешать, они
 * изначально находятся в контейнере
 * Class Value
 * @package fortress\core\di\holder
 */
class Value implements DependencyHolder {

    private $value;

    private function __construct($value) {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerInterface $dependencyContainer) {
        return $this->value;
    }

    public static function of($value) {
        return new Value($value);
    }

    public static function string($value) {
        if (!is_string($value)) {
            throw new InvalidArgumentException("Value must be type of string");
        }
        return new Value($value);
    }

    public static function number($value) {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException("Value must be numeric type (int or float)");
        }
        return new Value($value);
    }
}