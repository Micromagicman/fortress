<?php

namespace test\router;

use PHPUnit\Framework\TestCase;
use fortress\core\router\Router;

class RouterTest extends TestCase {

  private $router;

  protected function setUp() {
      $this->router = new Router();
  }

  protected function tearDown() {
      $this->router = NULL;
  }

  public function testRoute() {
  }
}
