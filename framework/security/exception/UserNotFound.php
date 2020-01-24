<?php

namespace fortress\security\exception;

use fortress\core\exception\FortressException;

class UserNotFound extends FortressException {

    private string $username;

    public function __construct(string $username) {
        parent::__construct("User '$username' not found");
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }
}