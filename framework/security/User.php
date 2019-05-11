<?php

namespace fortress\security;

interface User {
    public function getRoles();
    public function getUsername();
    public function getPassword();
}