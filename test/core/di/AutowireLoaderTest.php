<?php

namespace test\core\di;

use fortress\core\di\exception\DependencyNotFoundException;
use fortress\core\di\loader\AutowireLoader;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class A {
}

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
        $this->assertEquals(new B(new A()), $loader->load(B::class, $container));
    }

    public function testIsLoadable() {
        $loader = new AutowireLoader();
        $this->assertTrue($loader->isLoadable(A::class));
        $this->assertTrue($loader->isLoadable(B::class));
        $this->assertTrue($loader->isLoadable(TestCase::class));
        $this->assertFalse($loader->isLoadable("not class"));
    }
}