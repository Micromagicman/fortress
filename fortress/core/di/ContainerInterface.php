<?php

namespace fortress\core\di;

interface ContainerInterface {
    public function get(string $name);
    public function set(string $name, $value);
}