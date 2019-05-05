<?php

namespace fortress\core\controller;

use fortress\core\di\ContainerInterface;
use fortress\core\http\response\HtmlResponse;
use fortress\core\http\response\JsonResponse;
use fortress\core\http\response\RedirectResponse;
use fortress\core\view\PhpView;

abstract class Controller {

    private $container;

    public function __construct(ContainerInterface $ci) {
        $this->container = $ci;
    }

    protected function request() {
        return $this->container->get("request");
    }

    protected function dbConnection() {
        return $this->container->get("db.connection");
    }

    protected function parameter(string $name, $defaultValue = null) {
        return $this->container->getParameterOrDefault($name, $defaultValue);
    }

    protected function redirect(string $url) {
        return new RedirectResponse($url);
    }

    protected function json($data, int $statusCode = 200) {
        if (!is_array($data) && ($data instanceof \PDOStatement)) {
            $data = $data->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // TODO - throw
        }
        return new JsonResponse($data, $statusCode);
    }

    protected function render(string $templateName, array $data = [], int $statusCode = 200) {
        $view = new PhpView($templateName);
        $htmlContent = $view->render($data);
        return new HtmlResponse($htmlContent, $statusCode);
    }
}