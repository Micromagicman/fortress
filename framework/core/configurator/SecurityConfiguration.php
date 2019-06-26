<?php

namespace fortress\core\configurator;

use fortress\security\Authenticator;
use fortress\security\basic\BaseAuthenticator;
use fortress\security\basic\BaseRoleProvider;
use fortress\security\RoleProvider;
use fortress\security\User;
use fortress\security\UserProvider;
use Psr\Container\ContainerInterface;

class SecurityConfiguration extends Configuration {

    private const USER_PROVIDER_DATABASE = "fortress\security\provider\DatabaseUserProvider";
    private const USER_PROVIDER_CONFIGURATION = "fortress\security\provider\ConfigurationUserProvider";

    public function initialize(ContainerInterface $container, ...$configurations) {
        if (empty($configurations)) {
            return;
        }
        $configuration = $configurations[0];
        $container->set(UserProvider::class, $this->defineUserProvider($container, $configuration));
        $container->set(RoleProvider::class, BaseRoleProvider::class);
        $container->set(Authenticator::class, BaseAuthenticator::class);
        $this->loadUser($container);
    }

    private function loadUser(ContainerInterface $container) {
        $auth = $container->get(Authenticator::class);
        $user = $auth->loadUser();
        $container->set(User::class, $user);
    }

    private function defineUserProvider(ContainerInterface $container, ConfigurationBag $config) {
        $userProvider = $config
            ->get("user")
            ->get("provider");
        if (!$userProvider || !class_exists($userProvider)) {
            switch ($userProvider) {
                case "conf":
                case "config":
                case "configuration":
                    $usersFromConfiguration = $config->get("users")->items();
                    $container->setParameter("configurationUsers", $usersFromConfiguration);
                    return self::USER_PROVIDER_CONFIGURATION;
                case "db":
                case "database":
                default:
                    return self::USER_PROVIDER_DATABASE;
            }
        }
        return $userProvider;
    }
}