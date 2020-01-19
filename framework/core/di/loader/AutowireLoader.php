<?php

namespace fortress\core\di\loader;

use fortress\core\di\exception\DependencyNotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Автоматическая загрузка зависимостей
 * В случае, если перед разрешением зависимости, запрошенной через контейнер,
 * необходимо автоматически разрешить ее подзависимости, следует добавить данный лоадер в контейнер
 * Class AutowireLoader
 * @package fortress\core\di\loader
 */
class AutowireLoader implements Loader {

    /**
     * Загрузка зависимости по id
     * @param string $id
     * @param ContainerInterface $container
     * @return mixed
     * @throws DependencyNotFoundException
     */
    public function load(string $id, ContainerInterface $container) {
        try {
            $dependencyClass = new ReflectionClass($id);
            $requiredParameters = $this->resolveDependencies($dependencyClass);
            $arguments = [];
            foreach ($requiredParameters as $parameterId) {
                $arguments[] = $container->get($parameterId);
            }
            return $dependencyClass->newInstanceArgs($arguments);
        } catch (ReflectionException $e) {
            throw new DependencyNotFoundException($id);
        }
    }

    /**
     * Проверка на возможность загрузки данным лоадером
     * зависимости по переданному id
     * @param string $id
     * @return mixed
     */
    public function isLoadable(string $id) {
        return class_exists($id);
    }

    /**
     * Определение зависимостей, которые нееобходимо разрешить
     * для построения объекта переданного класса
     * @param ReflectionClass $class
     * @return array
     */
    private function resolveDependencies(ReflectionClass $class) {
        $constructor = $class->getConstructor();
        if (null === $constructor) {
            return [];
        }
        return $this->resolveMethodParameters($constructor);
    }

    /**
     * Определение типов параметров метода
     * Если в сигнатуре метода тип параметра не указан,
     * используется его имя в качестве id для разрешения зависимости
     * @param ReflectionMethod $method
     * @return array
     */
    private function resolveMethodParameters(ReflectionMethod $method) {
        $parameters = [];
        foreach ($method->getParameters() as $parameter) {
            $parameterClass = $parameter->getClass();
            $parameters[] = null !== $parameterClass
                ? $parameterClass->getName()
                : $parameter->getName();
        }
        return $parameters;
    }
}