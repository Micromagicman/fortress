<?php

namespace fortress\core\exception\handler;

use fortress\core\router\exception\RouteNotFound;
use fortress\core\view\ViewLoader;
use fortress\util\collection\ArrayUtils;
use fortress\util\common\StringUtils;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ResponseExceptionHandler implements ExceptionHandler {

    /**
     * Стандартный код ответа HTTP ошибки
     */
    private const DEFAULT_ERROR_STATUS_CODE = 500;

    /**
     * Соответствие типа исключения коду HTTP ответа
     */
    private const EXCEPTION_STATUS_CODES = [
        RouteNotFound::class => 404
    ];

    private ContainerInterface $container;

    private bool $devMode;

    public function __construct(ContainerInterface $container, bool $devMode = false) {
        $this->container = $container;
        $this->devMode = $devMode;
    }

    public function handle(ServerRequestInterface $request, Throwable $exception) {
        $responseBuilder = $this->getResponseBuilder($request);
        return $this->buildResponse($responseBuilder, $exception);
    }

    /**
     * Создание HTTP ответа в зависимости от режима разработки
     * @param ExceptionResponseBuilder $builder
     * @param Throwable $exception
     * @return ResponseInterface
     */
    private function buildResponse(ExceptionResponseBuilder $builder, Throwable $exception) {
        $statusCode = $this->resolveHttpStatusCode($exception);
        return $this->devMode
            ? $builder->developmentResponse($exception, $statusCode)
            : $builder->productionResponse($exception, $statusCode);
    }

    /**
     * В зависимости от заголовка запроса с типом переданных данных (Content-Type)
     * понимаем, какой тип ответа ожидает пользователь в качестве ошибки
     * @param ServerRequestInterface $request
     * @return ExceptionResponseBuilder
     */
    private function getResponseBuilder(ServerRequestInterface $request) {
        $contentType = $request->getHeaderLine("Content-Type");
        if (StringUtils::startsWith($contentType, "application/json")) {
            return new JsonException();
        }
        return new HtmlException($this->container->get(ViewLoader::class));
    }

    private function resolveHttpStatusCode(Throwable $exception) {
        return ArrayUtils::getOrDefault(
            self::EXCEPTION_STATUS_CODES,
            get_class($exception),
            self::DEFAULT_ERROR_STATUS_CODE
        );
    }
}