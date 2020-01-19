<?php

namespace fortress\core\di\exception;

use fortress\core\exception\FortressException;
use Psr\Container\NotFoundExceptionInterface;

class DependencyNotFoundException extends FortressException implements NotFoundExceptionInterface {

    private $id;

    public function __construct($id) {
        parent::__construct("Dependency with id '$id' not found");
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}