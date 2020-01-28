<?php

namespace test\core\controller;

use fortress\core\configuration\Configuration;
use fortress\core\controller\Controller;
use fortress\core\router\RouteCollection;
use fortress\core\router\Router;
use fortress\core\router\UriBuilder;
use fortress\core\view\ViewLoader;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use test\core\rotuer\ControllerA;
use test\core\rotuer\ControllerB;
use test\core\rotuer\ControllerC;
use test\core\rotuer\RouterTest;

class JsonController extends Controller {
    public function handle(ServerRequestInterface $request) {
        return $this->json(["data" => $request->getAttribute("data")], 201);
    }
}

class HtmlController extends Controller {
    public function handle(ServerRequestInterface $request) {
        return $this->render("test-template", [
            "title" => $request->getAttribute("title")
        ]);
    }
}

class RedirectController extends Controller {
    public function handle(ServerRequestInterface $request) {
        return $this->redirect("delete", ["id" => 1]);
    }
}

class RedirectPathController extends Controller {
    public function handle(ServerRequestInterface $request) {
        return $this->redirect("/api/test");
    }
}

class ControllerTest extends TestCase {

    public function testJson() {
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withAttribute("data", [1, 2, 3, 4]);
        $controller = new JsonController($this->createMock(ContainerInterface::class));
        $expected = new JsonResponse(["data" => [1, 2, 3, 4]], 201);
        $actual = $controller->handle($request);
        self::assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        self::assertEquals($expected->getPayload(), $actual->getPayload());
        self::assertEquals($expected->getHeaders(), $actual->getHeaders());
    }

    public function testRender() {
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withAttribute("title", "Fortress");
        $configuration = new Configuration(dirname(__FILE__) . "/../..");
        $viewLoader = new ViewLoader($configuration);
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method("get")
            ->willReturn($viewLoader);
        $controller = new HtmlController($containerMock);
        $expected = new HtmlResponse(
            "<!doctype html>\n"
                . "<html lang=\"en\">\n"
                . "<head>\n"
                . "    <meta charset=\"UTF-8\">\n"
                . "    <meta name=\"viewport\"\n"
                . "          content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">\n"
                . "    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">\n"
                . "    <title>Document</title>\n"
                . "</head>\n"
                . "<body>\n"
                . "<h1>Hello world</h1>\n"
                . "</body>\n"
                . "</html>",
            200
        );
        $actual = $controller->handle($request);
        self::assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        self::assertEquals($expected->getHeaders(), $actual->getHeaders());
        self::assertEquals($expected->getBody()->__toString(), $actual->getBody()->__toString());
    }

    public function testRedirect() {
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method("get")
            ->with(Router::class)
            ->willReturn($this->createRouterForRedirectResponse());
        $controller = new RedirectController($containerMock);
        $expected = new RedirectResponse("/api/1/delete");
        $actual = $controller->handle($this->createMock(ServerRequestInterface::class));
        self::assertEquals($expected->getHeaderLine("Location"), $actual->getHeaderLine("Location"));
        self::assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        self::assertEquals($expected->getHeaders(), $actual->getHeaders());
        $pathController = new RedirectPathController($containerMock);
        $expected = new RedirectResponse("/api/test");
        $actual = $pathController->handle($this->createMock(ServerRequestInterface::class));
        self::assertEquals($expected->getHeaderLine("Location"), $actual->getHeaderLine("Location"));
        self::assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        self::assertEquals($expected->getHeaders(), $actual->getHeaders());
    }

    private function createRouterForRedirectResponse() {
        $collection = new RouteCollection();
        $collection->get("index", "/", JsonController::class);
        $collection->post("api", "/api", HtmlController::class);
        $collection->get(
            "delete",
            "/api/{id:int}/delete",
            ControllerC::class,
            [ControllerA::class],
            [ControllerB::class, RouterTest::class]
        );
        return new Router(new UriBuilder(), $collection);
    }
}