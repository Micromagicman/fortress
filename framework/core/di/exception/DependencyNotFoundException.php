<?php

namespace fortress\core\di\exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class DependencyNotFoundException extends Exception implements NotFoundExceptionInterface {

    private $id;

    public function __construct($id) {
        parent::__construct("Dependency with id '$id' not found");
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}