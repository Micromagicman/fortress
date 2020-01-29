<?php

namespace fortress\core\http\response;

use Psr\Http\Message\ResponseInterface;

/**
 * Генератор http ответа
 * Interface ResponseEmitter
 * @package fortress\core\http
 */
interface ResponseEmitter {

    /**
     * Отправка http-ответа клиенту
     * @param ResponseInterface $response
     * @return mixed
     */
    public function emit(ResponseInterface $response);
}