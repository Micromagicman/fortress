<?php

namespace fortress\core\view\exception;

use fortress\core\exception\FortressException;

class TemplateNotFoundException extends FortressException {
    public function __construct(string $templatePath) {
        parent::__construct("Template not found: $templatePath");
    }
}