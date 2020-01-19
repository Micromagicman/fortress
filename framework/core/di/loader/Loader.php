<?php

namespace fortress\core\di\loader;

use Psr\Container\ContainerInterface;

/**
 * Абстрактный загрузчик зависимостей
 * Interface Loader
 * @package fortress\core\di\loader
 */
interface Loader {

    /**
     * Загрузка зависимости по id
     * @param string $id
     * @param ContainerInterface $container
     * @return mixed
     */
    public function load(string $id, ContainerInterface $container);

    /**
     * Проверка на возможность загрузки данным лоадером
     * зависимости по переданному id
     * @param string $id
     * @return mixed
     */
    public function isLoadable(string $id);
}