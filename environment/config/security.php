<?php

use fortress\core\configuration\Configuration;
use fortress\core\di\holder\Factory;
use fortress\core\di\loader\MapLoader;
use fortress\security\basic\BaseRoleProvider;
use fortress\security\basic\BaseUser;
use fortress\security\provider\DatabaseUserProvider;
use fortress\security\RoleProvider;
use fortress\security\Session;
use fortress\security\User;
use fortress\security\UserProvider;
use Psr\Container\ContainerInterface;

return new MapLoader([
    // Класс пользователя
    User::class => BaseUser::class,
    // Провайдеры пользователей и ролей
    UserProvider::class => DatabaseUserProvider::class,
    RoleProvider::class => BaseRoleProvider::class,
    // Вычислитель CSRF токена
    Configuration::CSRF_TOKEN_KEY => Factory::new(function (ContainerInterface $container) {
        return password_hash(
            $container->get(Session::class)->get(Configuration::CSRF_TOKEN_KEY),
            PASSWORD_DEFAULT
        );
    })
]);