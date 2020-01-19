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
        $this->assertEquals(1, $mapLoader->load("a", $container));
        $this->assertEquals(2, $mapLoader->load("b", $container));
        $this->expectException(DependencyNotFoundException::class);
        $mapLoader->load("c", $container);
    }

    public function testIsLoadable() {
        $mapLoader = new MapLoader(["a" => 1, "b" => 2]);
        $this->assertTrue($mapLoader->isLoadable("a"));
        $this->assertTrue($mapLoader->isLoadable("b"));
        $this->assertFalse($mapLoader->isLoadable("c"));
    }
}