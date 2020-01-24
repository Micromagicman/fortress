<?php

namespace fortress\core\exception\handler;

use Throwable;

/**
 * Построитель HTTP ответа в зависимости от выброшенного фреймворком исключения
 * Подробность информации в ответе зависит от режима разработки (dev, prod)
 * Interface ExceptionResponseBuilder
 * @package fortress\core\exception\handler
 */
interface ExceptionResponseBuilder {

    /**
     * HTTP ответ для режима разработки
     * @param Throwable $exception
     * @param int $statusCode
     * @return mixed
     */
    public function developmentResponse(Throwable $exception, int $statusCode);

    /**
     * HTTP ответ для production-режима
     * @param Throwable $exception
     * @param int $statusCode
     * @return mixed
     */
    public function productionResponse(Throwable $exception, int $statusCode);
}