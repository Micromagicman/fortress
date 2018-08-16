<?php

namespace fortress\core\logger;

interface Logger {

  public function printException($e);

  public function print(string $message);
}
