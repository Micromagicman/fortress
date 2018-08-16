<?php

namespace fortress\core\logger;

class HtmlLogger implements Logger {

  public function printException($e) {
    $exceptionFile = $this->wrap($e->getFile());
    $exceptionLine = $this->wrap($e->getLine());
    $exceptionMessage = $this->wrap($e->getMessage(), "b");
    $this->print("$exceptionFile line $exceptionLine: {$exceptionMessage}");
  }

  public function print(string $message) {
    echo $this->wrap($message, "p");
  }

  private function wrap($text, $tag = "span") {
    return "<$tag>$text</$tag>";
  }
}
