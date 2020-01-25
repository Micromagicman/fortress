<?php

namespace fortress\core\di;

/**
 * Абстракция над классами, отвечающими за вызов пользовательских функций
 * Interface Invoker
 * @package fortress\core\di
 */
interface Invoker {

    /**
     * Вызов пользовательской функции с массивом аргументов
     * @param $callable
     * @param array $arguments
     * @return mixed
     */
    public function invoke($callable, array $arguments = []);
}