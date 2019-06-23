<?php

namespace fortress\security\basic;

use fortress\core\exception\UserNotFound;
use fortress\security\Authenticator;
use fortress\security\Session;
use fortress\security\UserProvider;

class BaseAuthenticator implements Authenticator {

    private $session;

    private $userProvider;

    public function __construct(Session $session, UserProvider $userProvider) {
        $this->session = $session;
        $this->userProvider = $userProvider;
    }

    public function authenticate(string $username, string $password) {
        try {
            $user = $this->userProvider->byUsername($username);
            if (password_verify($password, $user->getPassword())) {
                $this->session->set("AUTHORIZED_USER", $user->serialize());
                return true;
            }
            return false;
        } catch (UserNotFound $e) {
            return false;
        }
    }

    public function logout() {
        $this->session->delete("AUTHORIZED_USER");
        return true;
    }

    public function loadUser() {
        $userData = $this->session->get("AUTHORIZED_USER");
        $user = new BaseUser();
        if (null != $userData) {
            $user->unserialize($userData);
        }
        return $user;
    }
}