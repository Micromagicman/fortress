<?php

namespace fortress\core\exception\handler;

use Psr\Http\Message\ResponseInterface;

interface ExceptionResponseBuilder {

    /**
     * @param int $statusCode
     * @return ExceptionResponseBuilder
     */
    public function setStatusCode(int $statusCode);

    /**
     * @param string $message
     * @return ExceptionResponseBuilder
     */
    public function setMessage(string $message);

    /**
     * @param array $trace
     * @return ExceptionResponseBuilder
     */
    public function setTrace(array $trace);

    /**
     * @return ResponseInterface
     */
    public function build();
}