<?php

namespace fortress\security;

interface UserInterface {
    public function getRoles();
    public function getUsername();
    public function getPassword();
}