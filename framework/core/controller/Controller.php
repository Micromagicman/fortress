<?php

namespace fortress\core\controller;

use fortress\core\di\ContainerInterface;
use fortress\core\http\response\JsonResponse;
use fortress\core\http\response\RedirectResponse;

abstract class Controller {

    private $container;

    public function __construct(ContainerInterface $ci) {
        $this->container = $ci;
    }

    protected function request() {
        return $this->container->get("request");
    }

    protected function parameter(string $name, $defaultValue = null) {
        return $this->container->getParameterOrDefault($name, $defaultValue);
    }

    protected function redirect(string $url) {
        return new RedirectResponse($url);
    }

    protected function json(array $data, int $statusCode = 200) {
        return new JsonResponse($data, $statusCode);
    }

    protected function render(string $templateName, array $data = []) {
        // TODO - рендеринг php шаблона        
    }
}