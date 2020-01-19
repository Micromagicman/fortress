<?php

use fortress\core\di\loader\MapLoader;
use fortress\security\basic\BaseRoleProvider;
use fortress\security\basic\BaseUser;
use fortress\security\provider\DatabaseUserProvider;
use fortress\security\RoleProvider;
use fortress\security\User;
use fortress\security\UserProvider;

return new MapLoader([
    User::class => BaseUser::class,
    UserProvider::class => DatabaseUserProvider::class,
    RoleProvider::class => BaseRoleProvider::class
]);