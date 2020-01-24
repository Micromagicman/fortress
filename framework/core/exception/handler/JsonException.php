<?php

namespace fortress\core\exception\handler;

use Laminas\Diactoros\Response\JsonResponse;
use Throwable;

class JsonException implements ExceptionResponseBuilder {

    /**
     * HTTP ответ для режима разработки
     * @param Throwable $exception
     * @param int $statusCode
     * @return mixed
     */
    public function developmentResponse(Throwable $exception, int $statusCode) {
        return $this->getResponse(
            [
                "status" => $statusCode,
                "message" => $exception->getMessage(),
                "trace" => $exception->getTrace(),
                "exceptionClass" => get_class($exception)
            ],
            $statusCode
        );
    }

    /**
     * HTTP ответ для production-режима
     * @param Throwable $exception
     * @param int $statusCode
     * @return mixed
     */
    public function productionResponse(Throwable $exception, int $statusCode) {
        return $this->getResponse(
            [
                "status" => $statusCode,
                "message" => $exception->getMessage(),
            ],
            $statusCode
        );
    }

    private function getResponse(array $data, int $statusCode) {
        return new JsonResponse($data, $statusCode);
    }
}