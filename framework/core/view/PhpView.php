<?php

namespace fortress\core\view;

class PhpView extends BaseView {
    
    public function render(array $variables = [], array $blocks = []) {
        return parent::render($variables, $blocks);
    }

    protected function getTemplateFolder() {
        return ".." . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "php";
    }

    protected function getTemplateExtension() {
        return "php";
    }
}