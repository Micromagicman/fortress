<?php

use fortress\core\di\ContainerBuilder;
use fortress\core\di\DependencyContainer;
use fortress\core\di\exception\DependencyNotFoundException;
use fortress\core\di\holder\Factory;
use fortress\core\di\holder\Value;
use fortress\core\di\loader\MapLoader;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class A {

    private B $b;

    private int $one;

    public function __construct(B $b, int $one) {
        $this->b = $b;
    }
}

class B {

    public function __construct() {
    }

    public function sum(int $a, int $b) {
        return $a + $b;
    }
}

class DependencyContainerTest extends TestCase {

    private DependencyContainer $dependencyContainer;

    public function setUp() {
        try {
            $this->dependencyContainer = (new ContainerBuilder())
                ->withLoaders(new MapLoader([
                    "a" => Value::string("a string"),
                    "c" => Factory::new(function() {
                        return 123;
                    }),
                    "one" => 1,
                    "two" => 2
                ]),
                new MapLoader([
                    "sum" => Factory::new(function(ContainerInterface $container) {
                        return $container->get("one") + $container->get("two");
                    })
                ]))
                ->useAutowiring()
                ->build();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }

    public function testGet() {
        $this->assertEquals(
            "a string",
            $this->dependencyContainer->get("a")
        );
        $this->assertEquals(
            123,
            $this->dependencyContainer->get("c")
        );
    }

    public function testGetNotExistingDependency() {
        $this->expectException(DependencyNotFoundException::class);
        $this->dependencyContainer->get("not dependency");
    }

    public function testGetNotStringKey() {
        $this->expectException(InvalidArgumentException::class);
        $this->dependencyContainer->get(1);
    }

    public function testGetAutowire() {
        $this->assertEquals(
            new A(new B(), 1),
            $this->dependencyContainer->get(A::class)
        );
    }

    public function testServiceFactoryDependency() {
        $this->assertEquals(
            3,
            $this->dependencyContainer->get("sum")
        );
    }

    public function testHas() {
        $this->assertFalse($this->dependencyContainer->has("not dependency"));
        $this->assertTrue($this->dependencyContainer->has("a"));
    }

    public function testInvoke() {
        $this->assertEquals(3, $this->dependencyContainer->invoke([new B(), "sum"], [1, 2]));
    }
}
