<?php

namespace fortress\core\middleware;

use fortress\core\Action;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Абстракция над классами, выполняющими предварительную
 * обработку HTTP запроса перед вызовом необходимого контроллера
 * Class BeforeAction
 * @package fortress\core\middleware
 */
abstract class BeforeAction extends MiddlewareAction {

    /**
     * @param $payload
     * @param callable $next
     * @return mixed
     */
    public function handle($payload, callable $next) {
        $this->checkPayload($payload);
        return $this->handleRequest($payload, $next);
    }

    /**
     * @return string
     */
    public function getPayloadType() {
        return ServerRequestInterface::class;
    }

    /**
     * Обработка HTTP-запроса
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     */
    protected abstract function handleRequest(ServerRequestInterface $request, callable $next);
}