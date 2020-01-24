<?php

namespace fortress\core\view;

use fortress\core\exception\TemplateNotFoundException;
use fortress\util\common\StringUtils;

abstract class View {

    private const TEMPLATE_DIR = ".." . DIRECTORY_SEPARATOR . "templates";

    protected string $templatePath;

    /**
     * Инициализация view
     * @param string $templateName
     * @throws TemplateNotFoundException
     */
    public function __construct(string $templateName) {
        $templatePath = $this->createTemplatePath($templateName);
        if (!file_exists($templatePath)) {
            throw new TemplateNotFoundException($templatePath);
        }
        $this->templatePath = $templatePath;
    }

    /**
     * Создание пути до шаблона view
     * @param string $templateName
     * @return string
     */
    protected function createTemplatePath(string $templateName) {
        $extension = $this->getExtension();
        if (!StringUtils::endsWith($templateName, ".$extension")) {
            $templateName .= ".$extension";
        }
        return self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $extension . DIRECTORY_SEPARATOR . $templateName;
    }

    protected abstract function getExtension();
}