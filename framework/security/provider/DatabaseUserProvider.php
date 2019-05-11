<?php

namespace fortress\security\provider;

use fortress\core\database\Database;
use fortress\core\exception\UserNotFound;
use fortress\security\basic\BaseUser;
use fortress\security\RoleProvider;
use fortress\security\UserProvider;

class DatabaseUserProvider implements UserProvider {

    private $dbConnection;

    private $roleProvider;

    public function __construct(Database $connection, RoleProvider $roleProvider) {
        $this->dbConnection = $connection;
        $this->roleProvider = $roleProvider;
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
        $userRoles = [];
        foreach ($this->roleProvider->getRoleMap() as $rCode => $rName) {
            if (($rolesCode & $rCode) == $rCode) {
                $userRoles[] = $rName;
            }
        }
        return $userRoles;
    }
}