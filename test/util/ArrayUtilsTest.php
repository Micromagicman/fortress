<?php

namespace test\util;

use fortress\util\collection\ArrayUtils;
use PHPUnit\Framework\TestCase;

class ArrayUtilsTest extends TestCase {

    public function testGerOrDefault() {
        self::assertEquals(
            ArrayUtils::getOrDefault([
                "a" => 1,
                "b" => 2
            ], "a"),
            1
        );
        self::assertNull(ArrayUtils::getOrDefault([
            "a" => 1,
            "b" => 2
        ], "c"));
        self::assertEquals(
            ArrayUtils::getOrDefault([
                "1" => 1,
                "b" => 2
            ], "1"),
            1
        );
    }
}