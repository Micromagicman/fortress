<?php

namespace fortress\security\basic;

use fortress\security\Authenticator;
use fortress\security\DatabaseUserProvider;
use fortress\security\Session;

class BaseAuthenticator implements Authenticator {

    private $session;

    private $userProvider;

    public function __construct(Session $session, DatabaseUserProvider $userProvider) {
        $this->session = $session;
        $this->userProvider = $userProvider;
    }

    public function authenticate(string $username, string $password) {
        $user = $this->userProvider->byUsername($username);
        if (password_verify($password, $user->getPassword())) {
            $this->session->set("AUTHORIZED_USER", $user->serialize());
            return true;
        }
        return false;
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