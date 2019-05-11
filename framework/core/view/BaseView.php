<?php

namespace fortress\core\view;

use fortress\core\exception\RenderException;
use fortress\core\exception\TemplateNotFoundException;

abstract class BaseView {

    private $parent = null; // get_called_class() instance!

    private $currentRenderingBlock;

    private $templatePath;

    private $content = "";

    private $blocks = [];

    public function __construct(string $templateFile) {
        $templatePath = $this->createTemplatePath($templateFile);
        if (!file_exists($templatePath)) {
            throw new TemplateNotFoundException($templatePath);
        }
        $this->templatePath = $templatePath;
    }

    public function extend(string $parentTemplateFile) {
        $this->appendContent();
        if (!empty($this->blocks) || !empty($this->content)) {
            throw new RenderException("'extend' command must be at first line of template");
        }

        $parentViewClass = get_called_class();
        $this->parent = new $parentViewClass($parentTemplateFile);
        ob_start();
    }

    public function start(string $blockName) {
        $this->appendContent();
        $this->currentRenderingBlock = $blockName;
        $this->content .= $this->createViewBlockTag($blockName);
        ob_start();
    }

    public function end() {
        if (null !== $this->currentRenderingBlock) {
            $this->blocks[$this->currentRenderingBlock] = ob_get_clean();
            $this->currentRenderingBlock = null;
        } else {
            ob_end_clean();
        }
        ob_start();
    }

    private function createTemplatePath(string $filename) {
        return $this->getTemplateFolder() . DIRECTORY_SEPARATOR . $filename . "." . $this->getTemplateExtension();
    }

    public function render(array $variables = [], array $blocks = []) {
        extract($variables);
        // Собираем ТОЛЬКО блоки
        ob_start();
        require_once $this->templatePath;
        $this->content .= ob_get_clean();
        // Заполнение родителя
        if (null !== $this->parent) {
            return $this->parent->render($variables, $this->blocks);
        }

        $this->assignBlocks($blocks);
        $this->injectBlocks();
        return $this->content;
    }

    private function injectBlocks() {
        foreach ($this->blocks as $blockName => $blockContent) {
            $this->content = str_replace(
                $this->createViewBlockTag($blockName), 
                $blockContent, 
                $this->content
            );
        }
    }

    private function assignBlocks(array $blocks) {
        foreach ($blocks as $blockName => $blockContent) {
            $this->blocks[$blockName] = $blockContent;
        }
    }

    private function appendContent() {
        if (ob_get_level() > 1) {
            $this->content .= trim(ob_get_clean());
        }
    }

    private function createViewBlockTag(string $blockName) {
        return "{--fortress.view." . $blockName . "--}"; 
    }

    protected abstract function getTemplateExtension();

    protected abstract function getTemplateFolder();
}