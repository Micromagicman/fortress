<?php

namespace fortress\core\controller;

use fortress\core\database\DatabaseConnection;
use fortress\core\http\response\HtmlResponse;
use fortress\core\http\response\JsonResponse;
use fortress\core\http\response\RedirectResponse;
use fortress\core\router\Router;
use fortress\core\view\PhpView;
use fortress\security\User;
use PDO;
use PDOStatement;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller {

    private ContainerInterface $container;

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

    protected function userIp() {
        return $this->request->getClientIp();
    }

    protected function redirect(string $to, array $uriParams = []) {
        $router = $this->container->get(Router::class);
        $uri = $router->buildUri($to, $uriParams);
        if (null !== $uri) {
            return new RedirectResponse($uri);
        }
        return new RedirectResponse($to);
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
        $templateType = $this->container->get("template.type");
        $templateDir = $this->container->get("template.dir");
        switch ($templateType) {
            default: return new PhpView($templateDir, $templateName);
        }
    }
}