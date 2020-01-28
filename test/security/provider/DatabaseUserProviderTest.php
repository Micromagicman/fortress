<?php

namespace test\security\provider;

use fortress\core\database\DatabaseConnection;
use fortress\security\basic\BaseUser;
use fortress\security\exception\UserNotFound;
use fortress\security\provider\DatabaseUserProvider;
use fortress\security\RoleProvider;
use PHPUnit\Framework\TestCase;

class DatabaseUserProviderTest extends TestCase {

    public function testByUsername() {
        $databaseMock = $this->createMock(DatabaseConnection::class);
        $roleProviderMock = $this->createMock(RoleProvider::class);
        $roleProviderMock->method("getRoleMap")
            ->willReturn([
                1 => "ROLE_GUEST",
                2 => "ROLE_USER",
                4 => "ROLE_ADMIN"
            ]);
        $provider = new DatabaseUserProvider($databaseMock, $roleProviderMock);
        $databaseMock->method("query")
            ->willReturn($databaseMock);
        $databaseMock->expects($this->at(1))
            ->method("fetchSingle")
            ->willReturn((object)[
                "id" => 100,
                "username" => "evgen",
                "email" => "evgen@micromagicman.ru",
                "password" => "123456",
                "role" => 7
            ]);
        self::assertEquals(
            new BaseUser(
                100,
                "evgen",
                "evgen@micromagicman.ru",
                "123456",
                ["ROLE_GUEST", "ROLE_USER", "ROLE_ADMIN"]
            ),
            $provider->byUsername("evgen")
        );
        $databaseMock->expects($this->at(1))
            ->method("fetchSingle")
            ->willReturn(null);
        $this->expectException(UserNotFound::class);
        $provider->byUsername("evgen");
    }
}