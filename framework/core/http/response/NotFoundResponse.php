<?php

namespace fortress\core\http\response;

use Symfony\Component\HttpFoundation\Response;

class NotFoundResponse extends Response {
    public function __construct(string $content = null) {
        parent::__construct($content, 404);
    }
}