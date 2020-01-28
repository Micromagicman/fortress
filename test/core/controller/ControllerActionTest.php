<?php

namespace test\core\controller;

use fortress\core\controller\Controller;
use fortress\core\controller\ControllerAction;
use fortress\core\router\Route;
use fortress\core\router\Router;
use Laminas\Diactoros\Response\JsonResponse;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class ATestController extends Controller {
    public function handle(ServerRequestInterface $request) {
        return new JsonResponse([], 404);
    }
}

class ControllerActionTest extends TestCase {

    public function testHandleRequest() {
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method("get")
            ->with(ATestController::class)
            ->willReturn(new ATestController($containerMock));

        $routerMock = $this->createMock(Router::class);
        $routerMock->method("match")
            ->with("/api/test", "POST")
            ->willReturn(new Route("route", "/api/test", ATestController::class));


        $uriMock = $this->createMock(UriInterface::class);
        $uriMock->method("getPath")
            ->willReturn("/api/test");
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method("getUri")
            ->willReturn($uriMock);
        $requestMock->method("getMethod")
            ->willReturn("POST");



        $action = new ControllerAction($containerMock, $routerMock);
        self::assertEquals(
            404,
            $action->handle($requestMock, function (ResponseInterface $response) {
                return $response->getStatusCode();
            })
        );
    }
}