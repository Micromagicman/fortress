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
        $notFoundTemplateName = $container->get("template.404");
        $templateDir = $container->get("template.dir");
        $content = $exception->getMessage();
        if (null != $notFoundTemplateName) {
            $view = new PhpView($templateDir, $notFoundTemplateName);
            $content = $view->render(["exception" => $exception]);
        }
        return new ErrorResponse($content, 404);
    }

    public static function ServerError(FortressException $e, ContainerInterface $container) {
        $content = get_class($e) . ": " . $e->getMessage() . " in file " . $e->getFile() . " on line " . $e->getLine();
        return new ErrorResponse($content, 500);
    }
}