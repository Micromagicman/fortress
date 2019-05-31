<?php

namespace fortress\core\configurator;

use fortress\security\Authenticator;
use fortress\security\basic\BaseAuthenticator;
use fortress\security\RoleProvider;
use fortress\security\User;
use fortress\security\UserProvider;
use Psr\Container\ContainerInterface;

class SecurityConfiguration extends Configuration {

    public function initialize(ContainerInterface $container, ...$configurations) {
        if (empty($configurations)) {
            return;
        }

        $configuration = $configurations[0];
        $userConfig = $configuration->get("user");
        $roleConfig = $configuration->get("role");

        $container->set(UserProvider::class, $userConfig->get("provider"));
        $container->set(RoleProvider::class, $roleConfig->get("provider"));
        $container->set(Authenticator::class, BaseAuthenticator::class);

        $auth = $container->get(Authenticator::class);
        $user = $auth->loadUser();
        $container->set(User::class, $user);
    }
}