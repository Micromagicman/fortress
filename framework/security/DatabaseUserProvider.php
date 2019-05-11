<?php

namespace fortress\security;

use fortress\core\database\Database;
use fortress\core\exception\UserNotFound;
use fortress\security\basic\BaseUser;

class DatabaseUserProvider implements UserProvider {

    private $dbConnection;

    public function __construct(Database $connection) {
        $this->dbConnection = $connection;
    }

    public function byUsername(string $username) {
        $userData = $this->fetchOneOrNull($username);
        if (null == $userData) {
            throw new UserNotFound($username);
        }
        return new BaseUser(
            $userData["username"],
            $userData["email"],
            $userData["password"],
            $this->mapRoles($userData["role"])
        );
    }

    private function fetchOneOrNull(string $username) {
        $selectStatement = $this->dbConnection->query(
            "SELECT * FROM f_user WHERE username=:username",
            ["username" => $username]
        );
        $matches = $selectStatement->fetchAll();
        return count($matches) > 0 ? $matches[0] : null;
    }

    private function mapRoles(int $rolesCode) {
        $roles = [
            1 => "ROLE_GUEST",
            2 => "ROLE_USER",
            4 => "ROLE_EDITOR",
            8 => "ROLE_STUFF",
            16 => "ROLE_ADMIN"
        ];
        $userRoles = [];
        foreach ($roles as $rCode => $rName) {
            if (($rolesCode & $rCode) == $rCode) {
                $userRoles[] = $rName;
            }
        }
        return $userRoles;
    }
}