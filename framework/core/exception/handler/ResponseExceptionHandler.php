<?php

namespace fortress\core\exception\handler;

use fortress\core\exception\RouteNotFound;
use fortress\util\common\StringUtils;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ResponseExceptionHandler implements ExceptionHandler {

    private const DEFAULT_ERROR_STATUS_CODE = 500;
    private const EXCEPTION_STATUS_CODES = [
        RouteNotFound::class => 404
    ];

    public function __construct(bool $devMode = false) {
    }

    public function handle(ServerRequestInterface $request, Throwable $exception) {
        return $this->getResponseBuilder($request)
            ->setMessage($exception->getMessage())
            ->setStatusCode($this->resolveHttpStatusCode($exception))
            ->build();
    }

    /**
     * @param ServerRequestInterface $request
     * @return ExceptionResponseBuilder
     */
    private function getResponseBuilder(ServerRequestInterface $request) {
        $contentType = $request->getHeaderLine("Content-Type");
        if (StringUtils::startsWith($contentType, "application/json")) {
            return new JsonException();
        }
        return new HtmlException();
    }

    private function resolveHttpStatusCode(Throwable $exception) {
        $exceptionClass = get_class($exception);
        if (array_key_exists($exceptionClass, self::EXCEPTION_STATUS_CODES)) {
            return self::EXCEPTION_STATUS_CODES[$exceptionClass];
        }
        return self::DEFAULT_ERROR_STATUS_CODE;
    }
}