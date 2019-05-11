<?php

namespace fortress\security\basic;

use fortress\security\UserInterface;
use Serializable;

class BaseUser implements UserInterface, Serializable {

    private $username;

    private $email;

    private $password;

    private $roles;

    public function __construct(
        string $username = "guest",
        string $email = "",
        string $password = "",
        array $roles = ["ROLE_GUEST"]
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function is(string $role) {
        return in_array($role, $this->roles);
    }

    /**
     * String representation of object
     */
    public function serialize() {
        return serialize([
            $this->username,
            $this->email,
            $this->password,
            $this->roles
        ]);
    }

    /**
     * Constructs the object
     */
    public function unserialize($serialized) {
        list(
            $this->username,
            $this->email,
            $this->password,
            $this->roles
            ) = unserialize($serialized);
    }
}