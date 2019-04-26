<?php

namespace fortress\core\http\response;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse extends Response {

    public function __construct(array $data = [], int $statusCode = 200) {
        parent::__construct(json_encode($data), $statusCode);
        $this->headers->set("Content-Type", "application/json; charset=utf-8");
    }
}