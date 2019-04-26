<?php

namespace fortress\core\http\response;

use Symfony\Component\HttpFoundation\Response;

class RedirectResponse extends Response {

    public function __construct(string $url) {
        parent::__construct("", 301);
        $this->headers->set("Location", $url);
    }
}