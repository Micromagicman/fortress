<?php

namespace test\security\provider;

use fortress\security\basic\BaseRoleProvider;
use fortress\security\basic\BaseUser;
use fortress\security\exception\UserNotFound;
use fortress\security\provider\ConfigurationUserProvider;
use PHPUnit\Framework\TestCase;

class ConfigurationUserProviderTest extends TestCase {

    public function testByUsername() {
        $configurationUserProvider = new ConfigurationUserProvider(new BaseRoleProvider(), [
            "evgen" => [
                "role" => 7,
                "email" => "evgen@micromagicman.ru",
                "password" => "123456"
            ]
        ]);
        self::assertEquals(
            new BaseUser(
                0,
                "evgen",
                "evgen@micromagicman.ru",
                "123456",
                ["ROLE_GUEST", "ROLE_USER", "ROLE_ADMIN"]
            ),
            $configurationUserProvider->byUsername("evgen")
        );
        $this->expectException(UserNotFound::class);
        $this->expectExceptionMessage("User 'evgeniy' not found");
        $configurationUserProvider->byUsername("evgeniy");
    }
}