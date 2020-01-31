<?php

use fortress\core\configuration\Configuration;
use fortress\core\di\holder\Factory;
use fortress\core\di\loader\MapLoader;
use fortress\security\Authenticator;
use fortress\security\basic\BaseAuthenticator;
use fortress\security\basic\BaseRoleProvider;
use fortress\security\provider\DatabaseUserProvider;
use fortress\security\RoleProvider;
use fortress\security\Session;
use fortress\security\User;
use fortress\security\UserProvider;
use Psr\Container\ContainerInterface;

return new MapLoader([
    // Класс пользователя
    User::class => Factory::new(function (ContainerInterface $container) {
        return $container->get(Authenticator::class)->loadUser();
    }),
    // Провайдеры пользователей и ролей
    UserProvider::class => Factory::new(function (ContainerInterface $container) {
        return $container->get(DatabaseUserProvider::class);
    }),
    RoleProvider::class => Factory::new(function (ContainerInterface $container) {
        return $container->get(BaseRoleProvider::class);
    }),
    Authenticator::class => Factory::new(function (ContainerInterface $container) {
        return $container->get(BaseAuthenticator::class);
    }),
    // Вычислитель CSRF токена
    Configuration::CSRF_TOKEN_KEY => Factory::new(function (ContainerInterface $container) {
        return password_hash(
            $container->get(Session::class)->get(Configuration::CSRF_TOKEN_KEY),
            PASSWORD_DEFAULT
        );
    })
]);