<?php

namespace fortress\core\middleware;

use Psr\Http\Message\ResponseInterface;

/**
 * Абстракция над классами, выполняющими последующую обработку (после вызова контроллера)
 * обработку HTTP ответа перед отдачей пользователю
 * Class AfterAction
 * @package fortress\core\middleware
 */
abstract class AfterAction extends MiddlewareAction {

    /**
     * @param $payload
     * @param callable $next
     * @return ResponseInterface
     */
    public function handle($payload, callable $next) {
        $this->checkPayload($payload);
        return $this->handleResponse($payload, $next);
    }

    /**
     * @return string
     */
    public function getPayloadType() {
        return ResponseInterface::class;
    }

    /**
     * Обработка HTTP-ответа
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     */
    protected abstract function handleResponse(ResponseInterface $response, callable $next);
}