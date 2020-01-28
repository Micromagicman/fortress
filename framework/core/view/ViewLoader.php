<?php

namespace fortress\core\view;

use fortress\core\configuration\Configuration;
use fortress\core\view\exception\TemplateNotFoundException;
use fortress\util\common\StringUtils;

class ViewLoader {

    private Configuration $configuration;

    public function __construct(Configuration $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * @param string $templateName
     * @param string $templateType
     * @return PhpView
     * @throws TemplateNotFoundException
     */
    public function load(string $templateName, string $templateType = Configuration::DEFAULT_TEMPLATE_TYPE) {
        if (Configuration::DEFAULT_TEMPLATE_TYPE === $templateType) {
            return new PhpView($this->createTemplateFilePath($templateName, $templateType));
        }
        throw new TemplateNotFoundException($templateName);
    }

    private function createTemplateFilePath(string $templateName, string $templateType) {
        if (!StringUtils::endsWith($templateName, ".$templateType")) {
            $templateName .= ".$templateType";
        }
        return $this->configuration->getTemplatesDir()
            . DIRECTORY_SEPARATOR
            . $templateType
            . DIRECTORY_SEPARATOR
            . $templateName;
    }
}