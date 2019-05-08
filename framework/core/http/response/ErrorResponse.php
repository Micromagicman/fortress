<?php

namespace fortress\core\http\response;

use fortress\core\exception\FortressException;
use fortress\core\view\PhpView;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends Response {
    public function __construct(string $content, int $statusCode = 500) {
        parent::__construct($content, $statusCode);
    }

    public static function NotFound(FortressException $exception, ContainerInterface $container) {
        $notFoundTemplatePath = $container->getParameter("template.404");
        $content = $exception->getMessage();
        if (null != $notFoundTemplatePath) {
            $view = new PhpView($notFoundTemplatePath);
            $content = $view->render(["exception" => $exception]);
        }
        return new ErrorResponse($content, 404);
    }

    public static function ServerError(FortressException $e, ContainerInterface $c) {
        return new ErrorResponse($e->getMessage(), 500);
    }
}