<?php

namespace test\core;

use fortress\core\Action;
use fortress\core\ActionPipeline;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MultiplyByTwo implements Action {
    public function handle($payload, callable $next) {
        return $next($payload * 2);
    }
}

class MultiplyByTenPlusTwo implements Action {
    public function handle($payload, callable $next) {
        return $next($payload * 10 + 2);
    }
}

class ActionPipelineTest extends TestCase {

    public function testRunMultiplication() {
        $actionPipeline = (new ActionPipeline($this->createMock(ContainerInterface::class)))
            ->pipe(new MultiplyByTwo())
            ->pipe(new MultiplyByTenPlusTwo());
        self::assertEquals(102, $actionPipeline->run(5));
    }

    public function testInvokeMultiplication() {
        $actionPipeline = (new ActionPipeline($this->createMock(ContainerInterface::class)))
            ->pipe(new MultiplyByTwo())
            ->pipe(new MultiplyByTenPlusTwo());
        self::assertEquals(1020, $actionPipeline(5, function ($payload) {
            return $payload * 10;
        }));
    }

    public function testRunMultiplicationWithContainerResolving() {
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->expects(self::exactly(2))
            ->method("get")
            ->with(self::logicalOr(MultiplyByTwo::class, MultiplyByTenPlusTwo::class))
            ->willReturn(new MultiplyByTwo(), new MultiplyByTenPlusTwo());
        $actionPipeline = (new ActionPipeline($containerMock))
            ->pipe(MultiplyByTwo::class)
            ->pipe(MultiplyByTenPlusTwo::class);
        self::assertEquals(102, $actionPipeline->run(5));
    }

    public function testRunMiltiplicationWithNotActionPipe() {
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->expects(self::exactly(2))
            ->method("get")
            ->with(self::logicalOr(MultiplyByTwo::class, ActionPipelineTest::class))
            ->willReturn(new MultiplyByTwo(), new ActionPipelineTest());
        $actionPipeline = (new ActionPipeline($containerMock))
            ->pipe(MultiplyByTwo::class)
            ->pipe(ActionPipelineTest::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            "Pipeline elements must implements %s, %s given",
            Action::class,
            ActionPipelineTest::class
        ));
        $actionPipeline->run(10);
    }
}