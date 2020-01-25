<?php

namespace fortress\core\router;

use fortress\core\exception\UriBuildException;

/**
 * Вспомогательный класс для работы с uri
 * Class UriBuilder
 * @package fortress\core\router
 */
class UriBuilder {

    public const URI_SEPARATOR = "/";
    public const URI_PARAMS_START = "?";

    /**
     * Создание пути с помощью разбитого на чати пути url и массива параметров
     * @param Route $route
     * @param array $params
     * @return string
     * @throws UriBuildException
     */
    public function buildPath(Route $route, array $params = []) {
        $buildChunks = [];
        foreach ($route->getPathChunks() as $key => $value) {
            if (is_string($key)) {
                if (!array_key_exists($key, $params)) {
                    throw new UriBuildException(
                        "$key parameter required to build {$route->getName()} uri"
                    );
                }
                $buildChunks[] = $params[$key];
            } else {
                $buildChunks[] = $value;
            }
        }
        return "/" . implode("/", $buildChunks);
    }

    /**
     * Разбиение uri на массив компонентов, находящихся между "/"
     * Например /some/api/route?param=1 будет разбит на массив ["some", "api", "route"]
     * @param string $uri
     * @return array
     */
    public function buildUriChunks(string $uri) {
        $purifiedUri = trim(strtok($uri, self::URI_PARAMS_START), self::URI_SEPARATOR);
        $chunks = [];
        foreach (explode(self::URI_SEPARATOR, $purifiedUri) as $chunk) {
            if (!empty($chunk)) {
                $chunks[] = $chunk;
            }
        }
        return $chunks;
    }
}