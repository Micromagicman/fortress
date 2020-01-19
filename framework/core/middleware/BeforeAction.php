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
abstract class BeforeAction implements Action {

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param $payload
     * @param callable $next
     * @return mixed
     */
    public function handle($payload, callable $next) {
        if (!($payload instanceof ServerRequestInterface)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Action payload must implements %s",
                    ServerRequestInterface::class
                )
            );
        }
        return $this->handleRequest($payload, $next);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * Обработка HTTP-запроса
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     */
    protected abstract function handleRequest(ServerRequestInterface $request, callable $next);
}