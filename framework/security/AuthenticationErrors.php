<?php

namespace fortress\security;

class AuthenticationErrors {

    private const ERRORS_SESSION_KEY = "AUTHENTICATION_ERRORS";

    private $session;

    public function __construct(Session $session) {
        $this->session = $session;
    }

    public function setErrors(string ...$errors) {
        $this->session->set(self::ERRORS_SESSION_KEY, $errors);
    }

    public function getLastErrors() {
        $errors = $this->session->get(self::ERRORS_SESSION_KEY);
        $this->session->delete(self::ERRORS_SESSION_KEY);
        return $errors;
    }
}