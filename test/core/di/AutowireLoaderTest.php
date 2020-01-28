<?php

namespace test\core\di;

use fortress\core\di\exception\DependencyNotFoundException;
use fortress\core\di\loader\AutowireLoader;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

// Test dependency #1
class A {
}

// Test dependency #2, depends on A
class B {
    public function __construct(A $a) {
    }
}

class AutowireLoaderTest extends TestCase {

    /**
     * @throws DependencyNotFoundException
     */
    public function testLoad() {
        $container = $this->createMock(ContainerInterface::class);
        $container->method("get")->willReturn(new A());
        $loader = new AutowireLoader();
        /** @var ContainerInterface $container */
        self::assertEquals(new B(new A()), $loader->load(B::class, $container));
    }

    public function testIsLoadable() {
        $loader = new AutowireLoader();
        self::assertTrue($loader->isLoadable(A::class));
        self::assertTrue($loader->isLoadable(B::class));
        self::assertTrue($loader->isLoadable(TestCase::class));
        self::assertFalse($loader->isLoadable("not class"));
    }
}