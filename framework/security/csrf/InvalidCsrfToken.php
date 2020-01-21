<?php

namespace fortress\security\csrf;

use fortress\core\exception\FortressException;
use Throwable;

class InvalidCsrfToken extends FortressException {

    private string $invalidToken;

    public function __construct(string $invalidToken) {
        parent::__construct(sprintf(
            "Invalid CSRF token - %s",
            $invalidToken
        ));
        $this->invalidToken = $invalidToken;
    }

    public function getInvalidToken() {
        return $this->invalidToken;
    }
}