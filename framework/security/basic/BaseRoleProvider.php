<?php

namespace fortress\security\basic;

use fortress\security\RoleProvider;

class BaseRoleProvider implements RoleProvider {
    public function getRoleMap() {
        return [
            1 => "ROLE_GUEST",
            2 => "ROLE_USER",
            4 => "ROLE_ADMIN"
        ];
    }
}