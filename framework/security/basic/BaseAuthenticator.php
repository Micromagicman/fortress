<?php

namespace fortress\security\basic;

use fortress\security\AuthenticationErrors;
use fortress\security\Authenticator;
use fortress\security\Session;
use fortress\security\UserProvider;

class BaseAuthenticator implements Authenticator {

    private const USER_SESSION_KEY = "AUTHORIZED_USER";

    private $session;
    private $userProvider;
    private $authenticationErrors;

    public function __construct(
        Session $session,
        UserProvider $userProvider,
        AuthenticationErrors $errors
    ) {
        $this->session = $session;
        $this->authenticationErrors = $errors;
        $this->userProvider = $userProvider;
    }

    public function authenticate(string $username, string $password) {
        try {
            $user = $this->userProvider->byUsername($username);
            if (password_verify($password, $user->getPassword())) {
                $this->session->set("AUTHORIZED_USER", $user->serialize());
                return true;
            }
            $this->authenticationErrors->setErrors("Неверное имя пользователя или пароль");
            return false;
        } catch (UserNotFound $e) {
            return false;
        }
    }

    public function logout() {
        $this->session->delete(self::USER_SESSION_KEY);
        return true;
    }

    public function loadUser() {
        $userData = $this->session->get(self::USER_SESSION_KEY);
        $user = new BaseUser();
        if (null != $userData) {
            $user->unserialize($userData);
        }
        return $user;
    }
}