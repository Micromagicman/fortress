<?php

namespace test\security\basic;

use fortress\security\basic\BaseUser;
use PHPUnit\Framework\TestCase;

class BaseUserTest extends TestCase {

    private BaseUser $user;

    public function setUp() {
        parent::setUp();
        $this->user = $this->createUser();
    }

    public function testRole() {
        self::assertTrue($this->user->is("ROLE_ADMIN"));
        self::assertFalse($this->user->is("ROLE_EDITOR"));
    }

    public function testSerialization() {
        self::assertEquals("a:5:{i:0;s:3:\"100\";i:1;s:5:\"evgen\";i:2;s:22:\"evgen@micromagicman.ru\";i:3;s:6:\"123456\";i:4;a:2:{i:0;s:9:\"ROLE_USER\";i:1;s:10:\"ROLE_ADMIN\";}}",$this->user->serialize());
        $unserialized = (new BaseUser());
        $unserialized->unserialize("a:5:{i:0;s:3:\"100\";i:1;s:5:\"evgen\";i:2;s:22:\"evgen@micromagicman.ru\";i:3;s:6:\"123456\";i:4;a:2:{i:0;s:9:\"ROLE_USER\";i:1;s:10:\"ROLE_ADMIN\";}}");
        self::assertEquals($this->createUser(), $unserialized);
        $this->user->unserialize($this->user->serialize());
        self::assertEquals($this->user, $this->createUser());
    }

    public function testGetters() {
        self::assertEquals("evgen@micromagicman.ru", $this->user->getEmail());
        self::assertEquals(100, $this->user->getId());
        self::assertEquals("123456", $this->user->getPassword());
        self::assertEquals("evgen", $this->user->getUsername());
        self::assertEquals( ["ROLE_USER", "ROLE_ADMIN"], $this->user->getRoles());
    }

    private function createUser() {
        return new BaseUser(
            100,
            "evgen",
            "evgen@micromagicman.ru",
            "123456",
            ["ROLE_USER", "ROLE_ADMIN"]
        );
    }
}