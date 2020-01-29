<?php

namespace fortress\core;

/**
 * Абстрактный обработчик http запроса
 * Interface Action
 * @package fortress\core\controller
 */
interface Action {

    /**
     * Выполнение действия
     * @param $payload
     * @param callable $next
     * @return mixed
     */
    public function handle($payload, callable $next);
}