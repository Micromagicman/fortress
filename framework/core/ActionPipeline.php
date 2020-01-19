<?php

namespace fortress\core;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;

/**
 * Конвейер для последовательной обработки данных
 * Class ActionPipeline
 * @package fortress\core
 */
class ActionPipeline {

    /**
     * Последовательность действий
     * @var array
     */
    private array $actions = [];

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Добавление нового этапа на последовательную обработку
     * @param mixed $action
     * @return ActionPipeline
     */
    public function pipe($action) {
        $this->actions[] = $action;
        return $this;
    }

    /**
     * Последовательное выполнение всех определенных
     * пользователе этапов обработки данных
     * @param $payload
     * @param callable $lastHandler
     * @return mixed
     */
    public function run($payload, callable $lastHandler = null) {
        if (!$current = $this->resolveNext()) {
            return null !== $lastHandler ? $lastHandler($payload) : $payload;
        }
        return $current->handle($payload, function ($payload) use ($lastHandler) {
            return $this->run($payload, $lastHandler);
        });
    }

    public function __invoke($payload, callable $lastHandler) {
        return $this->run($payload, $lastHandler);
    }

    /**
     * Подготовка следующего этапа к выполнению
     * Возвращает false - если следующего этапа нет
     * @return bool|mixed
     */
    private function resolveNext() {
        if (!$current = array_shift($this->actions)) {
            return false;
        }
        if (is_string($current)) {
            $current = $this->container->get($current);
        }
        if (!($current instanceof Action)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Pipeline elements must implements %s",
                    Action::class
                )
            );
        }
        return $current;
    }
}