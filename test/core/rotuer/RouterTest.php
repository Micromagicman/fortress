<?php

namespace test\core\rotuer;

use fortress\core\controller\Controller;
use fortress\core\exception\UriBuildException;
use fortress\core\router\exception\RouteNotFound;
use fortress\core\router\Route;
use fortress\core\router\RouteCollection;
use fortress\core\router\Router;
use fortress\core\router\RouterInitializer;
use fortress\core\router\UriBuilder;
use fortress\security\csrf\CsrfTokenValidator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ControllerA extends Controller {
    public function handle(ServerRequestInterface $request) {
        return $this->json(["msg" => "controllerA"]);
    }
}

class ControllerB extends Controller {
    public function handle(ServerRequestInterface $request) {
        return $this->json(["msg" => "controllerB"]);
    }
}

class ControllerC extends Controller {
    public function handle(ServerRequestInterface $request) {
        return $this->json(["msg" => "controllerC"]);
    }
}

class RouterTest extends TestCase {

    public function testMatch() {
        $routeCollection = $this->createRouteCollection();
        $router = new Router(new UriBuilder(), $routeCollection);
        self::assertEquals(
            new Route(
                "api",
                "/api",
                ControllerB::class,
                ["POST"]
            ),
            $router->match("/api", "POST")
        );
        $deleteRoute = new Route(
            "delete",
            "/api/{id:int}/delete",
            ControllerC::class,
            ["DELETE"]
        );
        $deleteRoute->addBeforeActions(ControllerA::class);
        $deleteRoute->addAfterActions(ControllerB::class, RouterTest::class);
        $deleteRoute->setPathVariables(["id" => 1]);
        self::assertEquals(
            $deleteRoute,
            $router->match("/api/1/delete", "DELETE")
        );
        $routeCollection->addPrefix("prefix");
        $routeCollection->addBeforeAction(RouterInitializer::class);
        $routeCollection->addAfterAction(CsrfTokenValidator::class);
        self::assertEquals(
            new Route(
                "index",
                "/prefix/",
                ControllerA::class,
                ["GET"],
                [RouterInitializer::class],
                [CsrfTokenValidator::class]
            ),
            $router->match("/prefix/", "GET")
        );
        $this->expectException(RouteNotFound::class);
        $router->match("/", "POST");
//        $this->expectException(RouteNotFound::class);
//        $router->match("/api/string/delete", "DELETE");
    }

    public function testBuildPath() {
        $router = new Router(new UriBuilder(), $this->createRouteCollection());
        self::assertEquals(
            "/api/255/delete",
            $router->buildPath("delete", ["id" => 255])
        );
        self::assertNull($router->buildPath("not found", []));
        $this->expectException(UriBuildException::class);
        $router->buildPath("delete", []);
    }

    private function createRouteCollection() {
        $collection = new RouteCollection();
        $collection->get("index", "/", ControllerA::class);
        $collection->post("api", "/api", ControllerB::class);
        $collection->delete(
            "delete",
            "/api/{id:int}/delete",
            ControllerC::class,
            [ControllerA::class],
            [ControllerB::class, RouterTest::class]
        );
        return $collection;
    }
}