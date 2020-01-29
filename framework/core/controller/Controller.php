<?php

namespace fortress\core\controller;

use fortress\core\configuration\Configuration;
use fortress\core\router\Router;
use fortress\core\view\exception\TemplateNotFoundException;
use fortress\core\view\View;
use fortress\core\view\ViewLoader;
use fortress\security\User;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use PDO;
use PDOStatement;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Абстрактный обработчик HTTP-запроса
 * Class Controller
 * @package fortress\core\controller
 */
abstract class Controller {

    private ContainerInterface $container;

    public function __construct(ContainerInterface $ci) {
        $this->container = $ci;
    }

    /**
     * Обработка контроллером HTTP-запроса
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public abstract function handle(ServerRequestInterface $request);

    protected function getContainer() {
        return $this->container;
    }

    /**
     * Перенаправление пользователя по переданому пути
     * Также, @param $to - может быть именен маршрута, определенного пользователем
     * в конфигурационном файле config/routes.php
     * @param array $uriParams
     * @return RedirectResponse
     */
    protected function redirect(string $to, array $uriParams = []) {
        $router = $this->container->get(Router::class);
        $uri = $router->buildPath($to, $uriParams);
        if (null !== $uri) {
            return new RedirectResponse($uri);
        }
        return new RedirectResponse($to);
    }

    /**
     * Отправка ответа в виде json
     * @param $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function json($data, int $statusCode = 200) {
        return new JsonResponse($data, $statusCode);
    }

    /**
     * Отправка ответа в виде html - страницы
     * @param string $templateName
     * @param array $data
     * @param int $statusCode
     * @return HtmlResponse
     * @throws TemplateNotFoundException
     */
    protected function render(string $templateName, array $data = [], int $statusCode = 200) {
        $view = $this->createView($templateName);
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
        $data[Configuration::CSRF_TOKEN_KEY] = $this->container->get(Configuration::CSRF_TOKEN_KEY);
        $data["user"] = $this->container->get(User::class);
        return $data;
    }

    /**
     * @param string $templateName
     * @return View
     * @throws TemplateNotFoundException
     */
    private function createView(string $templateName) {
        /** @var ViewLoader $loader */
        $loader = $this->container->get(ViewLoader::class);
        return $loader->load($templateName);
    }
}