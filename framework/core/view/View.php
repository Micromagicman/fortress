<?php

namespace fortress\core\view;

abstract class View {

    /**
     * Путь до файла view
     * @var string
     */
    protected string $templatePath;

    protected ViewLoader $viewLoader;

    /**
     * Инициализация view
     * @param string $templateFilePath
     */
    public function __construct(string $templateFilePath, ViewLoader $viewLoader) {
        $this->templatePath = $templateFilePath;
        $this->viewLoader = $viewLoader;
    }

    /**
     * Расширение файла шаблона view
     * @return string
     */
    protected abstract function getExtension();
}