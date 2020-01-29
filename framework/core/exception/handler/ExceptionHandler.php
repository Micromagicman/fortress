<?php

namespace fortress\core\exception\handler;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface ExceptionHandler {

    /**
     * Обработка исключения
     * @param ServerRequestInterface $request
     * @param Throwable $exception
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, Throwable $exception);
}