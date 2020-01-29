<?php

namespace test\core\di;

use fortress\core\di\exception\DependencyNotFoundException;
use fortress\core\di\loader\MapLoader;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MapLoaderTest extends TestCase {

    /**
     * @throws DependencyNotFoundException
     */
    public function testLoad() {
        $container = $this->createMock(ContainerInterface::class);
        $mapLoader = new MapLoader(["a" => 1, "b" => 2]);
        /** @var ContainerInterface $container */
        self::assertEquals(1, $mapLoader->load("a", $container));
        self::assertEquals(2, $mapLoader->load("b", $container));
        $this->expectException(DependencyNotFoundException::class);
        $mapLoader->load("c", $container);
    }

    public function testIsLoadable() {
        $mapLoader = new MapLoader(["a" => 1, "b" => 2]);
        self::assertTrue($mapLoader->isLoadable("a"));
        self::assertTrue($mapLoader->isLoadable("b"));
        self::assertFalse($mapLoader->isLoadable("c"));
    }
}