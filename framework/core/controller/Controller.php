<?php

namespace fortress\core\controller;

use fortress\core\di\ContainerInterface;

abstract class Controller {

    public function __construct(ContainerInterface $ci) {

    }

    public function getRequest() {
        return $this->ci->get("request");
    }
}