<?php

namespace fortress\core\di;

use fortress\core\di\exception\DependencyNotFoundException;
use fortress\core\di\loader\Loader;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Контейнер внедрения зависимостей
 * Class DependencyContainer
 * @package fortress\core\di
 */
class DependencyContainer implements ContainerInterface, Invoker {

    /**
     * Разрешенные зависимости
     * @var array
     */
    private array $resolved;

    /**
     * Загрузчики зависимостей
     * @var array|Loader[]
     */
    private array $loaders;

    public function __construct(Loader... $loaders) {
        $this->loaders = $loaders;
        $this->resolved = [
            ContainerInterface::class => $this,
            Invoker::class => $this,
            self::class => $this
        ];
    }

    /**
     * Поиск зависимоти по идентификатору.
     * @param string $id
     * @return mixed
     * @throws ContainerExceptionInterface - при ошибке в процессе построения зависимости
     * @throws NotFoundExceptionInterface - при отсутствии зависимости с переданным id в контейнере
     */
    public function get($id) {
        if (!is_string($id)) {
            throw new InvalidArgumentException("Dependency identifier must be a string");
        }
        if (array_key_exists($id, $this->resolved)) {
            return $this->resolved[$id];
        }
        $loader = $this->getLoader($id);
        if (null === $loader) {
            throw new DependencyNotFoundException($id);
        }
        $resolvedDependency = $loader->load($id, $this);
        $this->resolved[$id] = $resolvedDependency;
        return $resolvedDependency;
    }

    /**
     * Возвращает true, если контейнер может вернуть зависимость для переданного идентификатора.
     * В противном случае возвращает false.
     * @param string $id
     * @return bool
     */
    public function has($id) {
        return null !== $this->getLoader($id);
    }

    /**
     * Вызов пользовательской функции с массивом аргументов
     * @param $callable
     * @param array $arguments
     * @return mixed
     */
    public function invoke($callable, array $arguments = []) {
        return call_user_func_array($callable, $arguments);
    }

    /**
     * Поиск загрузчика для зависимости с переданным id
     * @param string $id
     * @return Loader|mixed|null
     */
    private function getLoader(string $id) {
        foreach ($this->loaders as $loader) {
            if ($loader->isLoadable($id)) {
                return $loader;
            }
        }
        return null;
    }
}