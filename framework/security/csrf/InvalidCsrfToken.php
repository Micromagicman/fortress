<?php

namespace fortress\security\csrf;

use fortress\core\exception\FortressException;

class InvalidCsrfToken extends FortressException {

    public function __construct() {
        parent::__construct("Invalid CSRF token");
    }
}