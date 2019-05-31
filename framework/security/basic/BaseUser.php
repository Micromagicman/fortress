<?php

namespace fortress\security\basic;

use fortress\security\User;
use Serializable;

class BaseUser implements User, Serializable {

    private $id;

    private $username;

    private $email;

    private $password;

    private $roles;

    public function __construct(
        string $id = "",
        string $username = "guest",
        string $email = "",
        string $password = "",
        array $roles = ["ROLE_GUEST"]
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
    }

    public function getId() {
        return $this->id;
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
            $this->id,
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
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->roles
            ) = unserialize($serialized);
    }
}