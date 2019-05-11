<?php

namespace fortress\security;

interface Authenticator {
    public function authenticate(string $username, string $password);
    public function logout();
    public function loadUser();
}