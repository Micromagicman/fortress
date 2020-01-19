<?php

namespace fortress\core\di\holder;

use Psr\Container\ContainerInterface;

/**
 * Обертка для зависимости
 * Interface DependencyLoader
 * @package fortress\core\di
 */
interface DependencyHolder {

    /**
     * Создание зависимостей с помощью уже имеющихся
     * в контейнере зависимостей
     * @param ContainerInterface $dependencyContainer
     * @return mixed
     */
    public function build(ContainerInterface $dependencyContainer);
}