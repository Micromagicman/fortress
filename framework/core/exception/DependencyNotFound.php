<?php

namespace fortress\core\exception;

class DependencyNotFound extends FortressException {

    private $id;

    public function __construct($id) {
        parent::__construct("Dependency with id '$id' not found");
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}