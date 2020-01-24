<?php

namespace fortress\core\http\response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class BasicResponseEmitter implements ResponseEmitter {

    /**
     * Отправка http-ответа клиенту
     * @param ResponseInterface $response
     */
    public function emit(ResponseInterface $response) {
        if (!headers_sent()) {
            $this->sendHeaders($response->getHeaders());
        }
        http_response_code($response->getStatusCode());
        $this->sendBody($response->getBody());
    }

    /**
     * Отправка тела ответа
     * @param StreamInterface $body
     */
    private function sendBody(StreamInterface $body) {
        echo $body;
    }

    /**
     * Отправка заголовков ответа
     * @param array $headers
     */
    private function sendHeaders(array $headers) {
        foreach ($headers as $name => $values) {
            foreach ($values as $headerValue) {
                header(sprintf("%s: %s", $name, $headerValue));
            }
        }
    }
}