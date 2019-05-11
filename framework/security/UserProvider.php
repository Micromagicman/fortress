<?php

namespace fortress\security;

interface UserProvider {
    public function byUsername(string $username);
}