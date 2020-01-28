<?php

namespace fortress\core\view;

use fortress\core\view\exception\RenderException;

class PhpView extends View {

    private PhpView $parent;

    private string $currentRenderingBlock;

    private string $content = "";

    private array $blocks = [];

    public function extend(string $parentTemplateFilePath) {
        $this->appendContent();
        if (!empty($this->blocks) || !empty($this->content)) {
            throw new RenderException("'extend' command must be at first line of template");
        }
        $this->parent = new PhpView($parentTemplateFilePath);
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

    public function render(array $variables = [], array $blocks = []) {
        extract($variables);
        // Собираем ТОЛЬКО блоки
        ob_start();
        require($this->templatePath);
        $this->content .= ob_get_clean();
        // Заполнение родителя
        if (isset($this->parent)) {
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
            $this->content .= ob_get_clean();
        }
    }

    private function createViewBlockTag(string $blockName) {
        return "{--fortress.view." . $blockName . "--}"; 
    }

    protected function getExtension() {
        return "php";
    }
}