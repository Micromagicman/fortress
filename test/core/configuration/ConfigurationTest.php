<?php

namespace test\core\configuration;

use fortress\core\configuration\Configuration;
use fortress\core\configuration\ConfigurationNotFoundException;
use fortress\core\di\loader\MapLoader;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase {

    public function testGetConfigFilePath() {
        $configuration = new Configuration(dirname(__FILE__) . "/../..", "test-config");
        self::assertEquals(
            [
                "one" => 1,
                "two" => 2,
                "sum" => 3
            ],
            $configuration->loadConfiguration("test-load")
        );
        $this->expectException(ConfigurationNotFoundException::class);
        $configuration->loadConfiguration("not-found-config");
    }

    public function testConfigure() {
        $configuration = new Configuration(dirname(__FILE__) . "/../..", "test-config");
        self::assertEquals(
            [
                new MapLoader([
                    "template.404" => "404",
                    "template.dir" => ".." . DIRECTORY_SEPARATOR . "templates",
                    "template.type" => "php"
                ])
            ],
            $configuration->configure()
        );
    }
}