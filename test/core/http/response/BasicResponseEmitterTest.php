<?php

namespace test\core\http\response;

use fortress\core\http\response\BasicResponseEmitter;
use Laminas\Diactoros\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class BasicResponseEmitterTest extends TestCase {

    public function testEmit() {
        $responseMock = $this->createMock(ResponseInterface::class);
        $body = new Stream("php://temp", "rw");
        $body->write("<html>hello world!</html>");
        $responseMock->method("getBody")
            ->willReturn($body);
        $emitter = new BasicResponseEmitter();
        ob_start();
        $emitter->emit($responseMock);
        self::assertEquals("<html>hello world!</html>", ob_get_clean());
    }

}