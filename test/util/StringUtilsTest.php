<?php

namespace test\util;

use fortress\util\common\StringUtils;
use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase {

    public function testStartsWith() {
        self::assertTrue(StringUtils::startsWith("margin-top", "margin"));
        self::assertTrue(StringUtils::startsWith("some-test-case", ""));
        self::assertFalse(StringUtils::startsWith("test-case", "case"));
        self::assertFalse(StringUtils::startsWith("test-case", "test-case-one"));
    }

    public function testEndsWith() {
        self::assertTrue(StringUtils::endsWith("margin-top", "top"));
        self::assertTrue(StringUtils::endsWith("some-test-case", ""));
        self::assertFalse(StringUtils::endsWith("test-case", "test"));
        self::assertFalse(StringUtils::endsWith("test-case", "test-case-one"));
    }

    public function testIsEmpty() {
        self::assertTrue(StringUtils::isEmpty(""));
        self::assertTrue(StringUtils::isEmpty(null));
        self::assertFalse(StringUtils::isEmpty("1"));
        self::assertTrue(StringUtils::isNotEmpty("2323"));
        self::assertFalse(StringUtils::isNotEmpty(null));
    }
}