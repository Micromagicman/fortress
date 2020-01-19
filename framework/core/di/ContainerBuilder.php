<?php

namespace fortress\core\di;

use fortress\core\di\loader\AutowireLoader;
use fortress\core\di\loader\Loader;

/**
 * Класс, отвечающий за построение
 * контейнера внедрения зависимостей
 * Class ContainerBuilder
 * @package fortress\core\di
 */
class ContainerBuilder {

    /**
     * Загрузчики зависимостей
     * @var array
     */
    private array $loaders = [];

    /**
     * Индикатор включенного режима автоматического разрешения зависимостей
     * @var bool
     */
    private bool $autowireMode = false;

    /**
     * Включить режим автоматического разрешения зависимострей
     * @return $this
     */
    public function useAutowiring() {
        if (!$this->autowireMode) {
            $this->loaders[] = new AutowireLoader();
            $this->autowireMode = true;
        }
        return $this;
    }

    /**
     * Добавление новых загрузчиков в контейнер
     * @param Loader ...$loaders
     * @return $this
     */
    public function withLoaders(Loader... $loaders) {
        foreach ($loaders as $loader) {
            if ($loader instanceof AutowireLoader) {
                $this->useAutowiring();
            } else {
                $this->loaders[] = $loader;
            }
        }
        return $this;
    }

    /**
     * Создание объекта контейнера
     * @return DependencyContainer
     */
    public function build() {
        return new DependencyContainer(...$this->loaders);
    }
}