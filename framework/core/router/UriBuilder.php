<?php

namespace fortress\core\router;

use fortress\core\exception\UriBuildException;

class UriBuilder {

    public function buildUri(Route $route, array $params = []) {
        $buildChunks = [];
        foreach ($route->getChunks() as $key => $value) {
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
        var_dump("/" . implode("/", $buildChunks));
        return "/" . implode("/", $buildChunks);
    }

    public function buildUriChunks(string $uri) {
        $purifiedUri = trim(strtok($uri, "?"), "/");
        return array_filter(explode("/", $purifiedUri), function ($chunk) {
            return !empty($chunk);
        });
    }
}