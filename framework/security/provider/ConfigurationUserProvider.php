<?php

namespace fortress\security\provider;

use fortress\security\basic\BaseUser;
use fortress\security\exception\UserNotFound;
use fortress\security\RoleProvider;
use fortress\security\UserProvider;

class ConfigurationUserProvider extends UserProvider {

    private $users = [];

    // Счетчик id пользователей
    private $lastUserId;

    public function __construct(RoleProvider $roleProvider, array $configurationUsers) {
        parent::__construct($roleProvider);
        $this->users = $configurationUsers;
        $this->lastUserId = 0;
    }
    
    public function byUsername(string $username) {
        if (!array_key_exists($username, $this->users)) {
            throw new UserNotFound($username);
        }
        $userData = $this->users[$username];
        return new BaseUser(
            $this->lastUserId++,
            $username,
            $userData["email"],
            $userData["password"],
            $this->mapRoles($userData["role"])
        );
    }
}