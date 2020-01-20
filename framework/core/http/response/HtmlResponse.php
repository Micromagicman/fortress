<?php

namespace fortress\core\http\response;

use Symfony\Component\HttpFoundation\Response;

class HtmlResponse extends Response {

    public function __construct(string $html, int $statusCode = 200) {
        parent::__construct($html, $statusCode);
        $this->headers->set("Content-Type", "text/html; charset=utf-8");
    }
}