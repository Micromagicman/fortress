<?php

namespace fortress\core\exception;

class RouteNotFound extends FortressException {

    private $uri;

    public function __construct($uri) {
        parent::__construct("Сannot find route for '$uri'");
    }

    public function getUri() {
        return $this->uri;
    }
}