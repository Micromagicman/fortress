<?php

namespace fortress\security;

use fortress\core\exception\SessionNotStarted;

class Session {

    private $storage;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            throw new SessionNotStarted("Start server session to work with security module");
        }
        $this->storage = &$_SESSION;
    }

    public function has(string $key) {
        return isset($this->storage[$key]);
    }

    public function get(string $key, $default = null) {
        return $this->has($key) ? $this->storage[$key] : $default;
    }

    public function set(string $key, $value) {
        $this->storage[$key] = $value;
    }

    public function delete(string $key) {
        unset($this->storage[$key]);
    }
}