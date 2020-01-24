<?php

namespace fortress\core\exception\handler;

use fortress\core\exception\TemplateNotFoundException;
use fortress\core\view\PhpView;
use Laminas\Diactoros\Response\HtmlResponse;
use Throwable;


class HtmlException implements ExceptionResponseBuilder {

    private const PRODUCTION_ERROR_PAGE = "errors/error.prod.php";
    private const DEVELOPMENT_ERROR_PAGE = "errors/error.dev.php";

    /**
     * HTTP ответ для режима разработки
     * @param Throwable $exception
     * @param int $statusCode
     * @return mixed
     * @throws TemplateNotFoundException
     */
    public function developmentResponse(Throwable $exception, int $statusCode) {
        return $this->createView(
            self::DEVELOPMENT_ERROR_PAGE,
            [
                "message" => $exception->getMessage(),
                "trace" => $exception->getTrace(),
                "exceptionClass" => get_class($exception)
            ],
            $statusCode
        );
    }

    /**
     * HTTP ответ для production-режима
     * @param Throwable $exception
     * @param int $statusCode
     * @return mixed
     * @throws TemplateNotFoundException
     */
    public function productionResponse(Throwable $exception, int $statusCode) {
        return $this->createView(
            self::PRODUCTION_ERROR_PAGE,
            [],
            $statusCode
        );
    }

    /**
     * @param string $templateName
     * @param array $data
     * @param int $statusCode
     * @return HtmlResponse
     * @throws TemplateNotFoundException
     */
    private function createView(string $templateName, array $data, int $statusCode) {
        $data["statusCode"] = $statusCode;
        $view = new PhpView($templateName);
        $html = $view->render($data);
        return new HtmlResponse($html, $statusCode);
    }
}