<?php

namespace fortress\core\view;

use fortress\core\exception\TemplateNotFoundException;

abstract class View {

    protected $templatePath;

    protected $templateDir;

    public function __construct(string $templateDir, string $templateName) {
        $templatePath = $this->createTemplatePath($templateDir, $templateName);
        if (!file_exists($templatePath)) {
            throw new TemplateNotFoundException($templatePath);
        }
        $this->templateDir = $templateDir;
        $this->templatePath = $templatePath;
    }

    public function getTemplateDir() {
        return $this->templateDir;
    }

    protected function createTemplatePath(string $templateDir, string $templateName) {
        $extension = $this->getExtension();
        if (!preg_match("/\.$extension$/", $templateName)) {
            $templateName .= ".$extension";
        }
        return $templateDir . DIRECTORY_SEPARATOR . $extension . DIRECTORY_SEPARATOR . $templateName;
    }

    protected abstract function getExtension();
}