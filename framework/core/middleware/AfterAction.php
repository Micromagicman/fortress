<?php

namespace fortress\core\middleware;

use fortress\core\Action;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Абстракция над классами, выполняющими последующую обработку (после вызова контроллера)
 * обработку HTTP ответа перед отдачей пользователю
 * Class AfterAction
 * @package fortress\core\middleware
 */
abstract class AfterAction implements Action {

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param $payload
     * @param callable $next
     * @return ResponseInterface
     */
    public function handle($payload, callable $next) {
        if (!$this->checkPayload($payload)) {
            throw new InvalidArgumentException(sprintf(
                "Action payload must be following array [%s, %s]",
                ServerRequestInterface::class,
                ResponseInterface::class
            ));
        }
        return $this->handleResponse($payload[0], $payload[1], $next);
    }

    /**
     * Проверка, что переданные по пайплайну параметры соответствуют
     * необходимым для дальнейшего продвижения по конвейеру типам
     * @param $payload
     * @return bool
     */
    private function checkPayload($payload) {
        if (!is_array($payload) || count($payload) < 2) {
            return false;
        }
        foreach ([ServerRequestInterface::class, ResponseInterface::class] as $type) {
            if (!(array_shift($payload) instanceof $type)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * Обработка HTTP-ответа
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     */
    protected abstract function handleResponse(ServerRequestInterface $request, ResponseInterface $response, callable $next);
}