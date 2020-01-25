<?php

namespace fortress\security\provider;

use fortress\core\database\DatabaseConnection;
use fortress\core\exception\UserNotFound;
use fortress\security\basic\BaseUser;
use fortress\security\RoleProvider;
use fortress\security\UserProvider;

class DatabaseUserProvider extends UserProvider {

    private $dbConnection;

    public function __construct(DatabaseConnection $connection, RoleProvider $roleProvider) {
        parent::__construct($roleProvider);
        $this->dbConnection = $connection;
    }

    public function byUsername(string $username) {
        $userData = $this->fetchUser($username);
        if (null == $userData) {
            throw new UserNotFound($username);
        }
        return new BaseUser(
            $userData->id,
            $userData->username,
            $userData->email,
            $userData->password,
            $this->mapRoles($userData->role)
        );
    }

    private function fetchUser(string $username) {
        return $this->dbConnection
            ->query(
                "SELECT id, username, email, password, role FROM f_user WHERE username=:username",
                ["username" => $username]
            )
            ->fetchSingle();
    }
}