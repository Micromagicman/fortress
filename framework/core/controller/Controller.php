<?php

namespace fortress\core\controller;

use fortress\core\database\DatabaseConnection;
use fortress\core\http\response\HtmlResponse;
use fortress\core\http\response\JsonResponse;
use fortress\core\http\response\RedirectResponse;
use fortress\core\view\PhpView;
use fortress\security\User;
use PDO;
use PDOStatement;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller {

    private $container;

    private $request;

    private $user;

    public function __construct(ContainerInterface $ci) {
        $this->container = $ci;
        $this->request = $this->container->get(Request::class);
        $this->user = $this->container->get(User::class);
    }

    protected function di() {
        return $this->container;
    }

    protected function request() {
        return $this->request;
    }

    protected function post(string $key, $default = null) {
        return $this->request->request->get($key, $default);
    }

    protected function query(string $key, $default = null) {
        return $this->request->query->get($key, $default);
    }

    protected function dbConnection() {
        return $this->container->get(DatabaseConnection::class);
    }

    protected function user() {
        return $this->user;
    }

    protected function parameter(string $name, $defaultValue = null) {
        return $this->container->getParameterOrDefault($name, $defaultValue);
    }

    protected function redirect(string $url) {
        return new RedirectResponse($url);
    }

    protected function json($data, int $statusCode = 200) {
        return new JsonResponse($this->processDataBeforeOutput($data), $statusCode);
    }

    protected function render(string $templateName, array $data = [], int $statusCode = 200) {
        $view = $this->createView($templateName);
        $data["user"] = $this->container->get(User::class);
        $htmlContent = $view->render($this->processDataBeforeOutput($data));
        return new HtmlResponse($htmlContent, $statusCode);
    }

    private function processDataBeforeOutput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($value instanceof PDOStatement) {
                    $data[$key] = $value->fetchAll(PDO::FETCH_ASSOC);
                }
            }
        } else if ($data instanceof PDOStatement) {
            $data = $data->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }

    private function createView(string $templateName) {
        $templateType = $this->container->getParameter("template.type");
        $templateDir = $this->container->getParameter("template.dir");
        switch ($templateType) {
            default: return new PhpView($templateDir, $templateName);
        }
    }
}