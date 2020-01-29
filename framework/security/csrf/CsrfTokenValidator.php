<?php

namespace fortress\security\csrf;

use fortress\core\configuration\Configuration;
use fortress\core\middleware\BeforeAction;
use fortress\security\Session;
use fortress\util\common\StringUtils;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfTokenValidator extends BeforeAction {

    /**
     * Безопасные методы
     * Для данных методов нет необходимости проверять CSRF токен
     */
    private const SAFE_METHODS = ["GET", "HEAD", "OPTIONS", "TRACE"];

    /**
     * Ключ, по которому хранится токен
     * - В полях формы (тег <input type="hidden" />)
     * - В Cookies
     * - В пользовательской сессии на стороне сервера
     */
    private const TOKEN_KEY = Configuration::CSRF_TOKEN_KEY;

    /**
     * Ключ HTTP заголовка, содержащего токен
     */
    private const TOKEN_HEADER_KEY = "Csrf-Token";

    /**
     * Серверная сессия
     * @var Session
     */
    private Session $session;

    public function __construct(ContainerInterface $container, Session $session) {
        parent::__construct($container);
        $this->session = $session;
    }

    /**
     * Валидация токена
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed|ResponseInterface
     * @throws InvalidCsrfToken
     */
    protected function handleRequest(ServerRequestInterface $request, callable $next) {
        if ($this->isSafeRequest($request) || $this->matchTokens($request)) {
           return $this->addTokenToResponse($next($request));
        }
        throw new InvalidCsrfToken();
    }

    /**
     * Добавление CSRF токена к заголовкам ответа
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function addTokenToResponse(ResponseInterface $response) {
        return $response->withHeader(self::TOKEN_HEADER_KEY, $this->generateToken());
    }

    /**
     * Проверка корректности полученного из запроса токена
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function matchTokens(ServerRequestInterface $request) {
        return password_verify(
            $this->session->get(self::TOKEN_KEY),
            $this->getRequestToken($request)
        );
    }

    /**
     * Генерация токена на основе ключа, хранящегося в пользовательской сессии
     * @return string
     */
    private function generateToken() {
        if (!$this->session->has(self::TOKEN_KEY)) {
            $this->session->set(self::TOKEN_KEY, uniqid("", true));
        }
        return $this->getContainer()->get(self::TOKEN_KEY);
    }

    /**
     * Проверка, является ли HTTP метод запроса безопасным
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function isSafeRequest(ServerRequestInterface $request) {
        return in_array($request->getMethod(), self::SAFE_METHODS);
    }

    /**
     * Получение CSRF токена из одного из доступных источников
     * Очередность: POST данные формы -> Заголовки запроса -> Куки
     * @param ServerRequestInterface $request
     * @return mixed|string
     */
    private function getRequestToken(ServerRequestInterface $request) {
        $token = $request->getParsedBody()[self::TOKEN_KEY]
            ?? $request->getHeaderLine(self::TOKEN_HEADER_KEY);
        if (StringUtils::isEmpty($token)) {
            $token = $request->getCookieParams()[self::TOKEN_KEY] ?? "";
        }
        return $token;
    }
}