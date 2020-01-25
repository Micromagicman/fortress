<?php

namespace fortress\core\middleware;

use fortress\core\Action;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

abstract class MiddlewareAction implements Action {

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Проверка, что переданный по пайплайну параметр соответствует
     * необходимым для дальнейшего продвижения по конвейеру типам
     * @param $payload
     * @return void
     */
    protected function checkPayload($payload) {
        $payloadType = $this->getPayloadType();
        if (!($payload instanceof $payloadType)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Action payload must implements %s",
                    $payloadType
                )
            );
        }
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer() {
        return $this->container;
    }

    /**
     * Тип передаваемого по конвейеру параметра
     * @return string
     */
    protected abstract function getPayloadType();
}