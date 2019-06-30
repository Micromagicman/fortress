<?php

namespace fortress\security;

abstract class UserProvider {

    protected $roleProvider;

    protected function __construct(RoleProvider $roleProvider) {
        $this->roleProvider = $roleProvider;
    }

    protected function mapRoles(int $rolesCode) {
        $userRoles = [];
        foreach ($this->roleProvider->getRoleMap() as $rCode => $rName) {
            if (($rolesCode & $rCode) == $rCode) {
                $userRoles[] = $rName;
            }
        }
        return $userRoles;
    }

    public abstract function byUsername(string $username);
}