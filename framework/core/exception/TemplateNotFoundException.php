<?php

namespace fortress\core\exception;

class TemplateNotFoundException extends FortressException {

    public function __construct(string $templatePath) {
        parent::__construct("Template not found: $templatePath");
    }
}