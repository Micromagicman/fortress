<?php

namespace fortress\core\view;

abstract class View {

    /**
     * Путь до файла view
     * @var string
     */
    protected string $templatePath;

    /**
     * Инициализация view
     * @param string $templateFilePath
     */
    public function __construct(string $templateFilePath) {
        $this->templatePath = $templateFilePath;
    }

    /**
     * Расширение файла шаблона view
     * @return string
     */
    protected abstract function getExtension();
}