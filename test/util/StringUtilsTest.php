<?php

namespace test\util;

use fortress\util\common\StringUtils;
use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase {

    public function testStartsWith() {
        $this->assertTrue(StringUtils::startsWith("margin-top", "margin"));
        $this->assertTrue(StringUtils::startsWith("some-test-case", ""));
        $this->assertFalse(StringUtils::startsWith("test-case", "case"));
    }

    public function testEndsWith() {
        $this->assertTrue(StringUtils::endsWith("margin-top", "top"));
        $this->assertTrue(StringUtils::endsWith("some-test-case", ""));
        $this->assertFalse(StringUtils::endsWith("test-case", "test"));
    }
}