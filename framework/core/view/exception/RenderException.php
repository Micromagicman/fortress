<?php

namespace fortress\core\view\exception;

use fortress\core\exception\FortressException;
use Throwable;

class RenderException extends FortressException {
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}