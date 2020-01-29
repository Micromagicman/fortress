<?php

namespace fortress\core\di\loader;

use fortress\core\di\exception\DependencyNotFoundException;
use fortress\core\di\holder\DependencyHolder;
use Psr\Container\ContainerInterface;

/**
 * Загрузчик зависимостей из ассоциативного массива
 * Данный загрузчик ограничивается разрешением зависимостей лишь в рамках
 * переданного ему ассоциативного массива, где ключом является id зависимости.
 * Class MapLoader
 * @package fortress\core\di\loader
 */
class MapLoader implements Loader {

    /**
     * Набор зависимостей
     * @var array
     */
    private array $map = [];

    public function __construct(array $map) {
        $this->map = $map;
    }

    /**
     * Загрузка зависимости по id
     * @param string $id
     * @param ContainerInterface $container
     * @return mixed
     * @throws DependencyNotFoundException
     */
    public function load(string $id, ContainerInterface $container) {
        if (!$this->isLoadable($id)) {
            throw new DependencyNotFoundException($id);
        }
        $value = $this->map[$id];
        if ($value instanceof DependencyHolder) {
            $value = $value->build($container);
            $this->map[$id] = $value;
        }
        return $value;
    }

    /**
     * Проверка на возможность загрузки данным лоадером
     * зависимости по переданному id
     * @param string $id
     * @return mixed
     */
    public function isLoadable(string $id) {
        return array_key_exists($id, $this->map);
    }
}