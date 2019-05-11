<?php

namespace fortress\core\exception;

class UserNotFound extends FortressException {

    private $username;

    public function __construct(string $username) {
        parent::__construct("User '$username' not found");
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }
}