<?php

namespace test\core\rotuer;

use fortress\cache\RouteCacheManager;
use fortress\core\configuration\Configuration;
use fortress\core\router\Route;
use fortress\core\router\RouteCollection;
use fortress\core\router\RouterInitializer;
use fortress\security\csrf\CsrfTokenValidator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouterInitializerTest extends TestCase {

    public function testHandleRequest() {
        $containerMock = $this->createMock(ContainerInterface::class);
        $cacheMock = $this->createMock(RouteCacheManager::class);
        $cacheMock->method("restore")
            ->willReturn([
                new Route(
                    "index",
                    "/prefix/",
                    ControllerA::class,
                    ["GET"],
                    [RouterInitializer::class],
                    [CsrfTokenValidator::class]
                ),
                new Route(
                    "delete",
                    "/api/{id:int}/delete",
                    ControllerC::class,
                    ["DELETE"]
                )
            ]);
        $actualCollection = new RouteCollection();
        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->method("loadConfiguration")
            ->with(Configuration::ROUTES_CONFIGURATION_NAME)
            ->willReturn([
                function(RouteCollection $rc) {
                    $rc->post("welcome", "/", RouterInitializerTest::class, [RouterInitializerTest::class], [RouterInitializerTest::class]);
                },
            ]);
        $initializer = new RouterInitializer($containerMock, $configurationMock, $cacheMock, $actualCollection);
        $initializer->handle($this->createMock(ServerRequestInterface::class), function () {});
        $expectedCollection = new RouteCollection();
        $expectedCollection->addRoute(
            "index",
            new Route(
                "index",
                "/prefix/",
                ControllerA::class,
                ["GET"],
                [RouterInitializer::class],
                [CsrfTokenValidator::class]
            )
        );
        $expectedCollection->addRoute(
            "delete",
            new Route(
                "delete",
                "/api/{id:int}/delete",
                ControllerC::class,
                ["DELETE"]
            )
        );
        $expectedCollection->addRoute(
            "index",
            new Route(
                "index",
                "/prefix/",
                ControllerA::class,
                ["GET"],
                [RouterInitializer::class],
                [CsrfTokenValidator::class]
            )
        );
        $expectedCollection->addRoute(
            "welcome",
            new Route(
                "welcome",
                "/",
                RouterInitializerTest::class,
                ["POST"],
                [RouterInitializerTest::class],
                [RouterInitializerTest::class]
            )
        );
        self::assertEquals($expectedCollection, $actualCollection);
    }
}