<?php

namespace fortress\security\csrf;

use fortress\core\middleware\BeforeAction;
use fortress\security\Session;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfTokenValidator extends BeforeAction {

    private const SAFE_METHODS = ["GET", "HEAD", "OPTIONS", "TRACE"];
    private const TOKEN_KEY = "X-CSRF-TOKEN";

    private Session $session;

    public function __construct(ContainerInterface $container, Session $session) {
        parent::__construct($container);
        $this->session = $session;
    }

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed|ResponseInterface
     * @throws InvalidCsrfToken
     */
    protected function handleRequest(ServerRequestInterface $request, callable $next) {
        if ($this->isSafeRequest($request) || $this->matchTokens($request)) {
            return $this->addTokenToResponse($next($request));
        }
        throw new InvalidCsrfToken($this->getRequestToken($request) ?: "<empty>");
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function addTokenToResponse(ResponseInterface $response) {
        $bytes = bin2hex(random_bytes(16));
        $this->session->set(self::TOKEN_KEY, $bytes);
        return $response->withHeader(self::TOKEN_KEY, $bytes);
    }

    private function matchTokens(ServerRequestInterface $request) {
        $requestToken = $this->getRequestToken($request);
        var_dump($requestToken);
        return $requestToken === $this->session->get(self::TOKEN_KEY);
    }

    private function isSafeRequest(ServerRequestInterface $request) {
        return in_array($request->getMethod(), self::SAFE_METHODS);
    }

    private function getRequestToken(ServerRequestInterface $request) {
        $token = $request->getHeader(self::TOKEN_KEY) ?: $request->getHeader("Cookie");
        if (!empty($token)) {
            return $token[0];
        }
    }
}