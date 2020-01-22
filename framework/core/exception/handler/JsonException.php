<?php

namespace fortress\core\exception\handler;

use Laminas\Diactoros\Response\JsonResponse;

class JsonException implements ExceptionResponseBuilder {

    private array $data = [];

    private int $status;

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode(int $statusCode) {
        $this->status = $statusCode;
        $this->data["status"] = $statusCode;
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message) {
        $this->data["message"] = $message;
        return $this;
    }

    public function setTrace(array $trace) {
        // TODO: Implement setTrace() method.
    }

    public function build() {
        return new JsonResponse($this->data, $this->status);
    }
}