<?php

namespace test\security\basic;

use fortress\security\AuthenticationErrors;
use fortress\security\basic\BaseAuthenticator;
use fortress\security\basic\BaseUser;
use fortress\security\exception\UserNotFound;
use fortress\security\Session;
use fortress\security\UserProvider;
use PHPUnit\Framework\TestCase;

class BaseAuthenticatorTest extends TestCase {

    public function testAuthenticate() {
        $sessionMock = $this->createMock(Session::class);
        $authenticationErrorsMock = $this->createMock(AuthenticationErrors::class);
        $userProviderMock = $this->createMock(UserProvider::class);
        $userProviderMock->method("byUsername")
            ->with("evgen")
            ->willReturn(new BaseUser(
                1,
                "evgen",
                "evgen@micromagicman.ru",
                password_hash("123456", PASSWORD_DEFAULT),
                []
            ));
        $authenticator = new BaseAuthenticator($sessionMock, $userProviderMock, $authenticationErrorsMock);
        self::assertTrue($authenticator->authenticate("evgen", "123456"));
        self::assertFalse($authenticator->authenticate("evgen", "654321"));
        $userProviderMock->method("byUsername")
            ->withAnyParameters()
            ->willThrowException(new UserNotFound("unknown"));
        self::assertFalse($authenticator->authenticate("evgeniy", "123456"));
    }

    public function testLogout() {
        $session = new Session();
        $session->set("AUTHORIZED_USER", "evgen");
        $authenticationErrorsMock = $this->createMock(AuthenticationErrors::class);
        $userProviderMock = $this->createMock(UserProvider::class);
        $authenticator = new BaseAuthenticator($session, $userProviderMock, $authenticationErrorsMock);
        self::assertTrue($session->has("AUTHORIZED_USER"));
        $authenticator->logout();
        self::assertFalse($session->has("AUTHORIZED_USER"));
    }

    public function testLoadUser() {
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method("get")
            ->with("AUTHORIZED_USER")
            ->willReturn("a:5:{i:0;s:3:\"100\";i:1;s:5:\"evgen\";i:2;s:22:\"evgen@micromagicman.ru\";i:3;s:6:\"123456\";i:4;a:2:{i:0;s:9:\"ROLE_USER\";i:1;s:10:\"ROLE_ADMIN\";}}");
        $authenticationErrorsMock = $this->createMock(AuthenticationErrors::class);
        $userProviderMock = $this->createMock(UserProvider::class);
        $authenticator = new BaseAuthenticator($sessionMock, $userProviderMock, $authenticationErrorsMock);
        self::assertEquals(
            new BaseUser(100, "evgen", "evgen@micromagicman.ru", "123456", ["ROLE_USER", "ROLE_ADMIN"]),
            $authenticator->loadUser()
        );
    }
}