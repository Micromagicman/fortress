<?php

namespace fortress\core\di;

interface ContainerInterface {
    public function get(string $name);
    public function set(string $name, $value);
    public function getParameter(string $name);
    public function getParameterOrDefault(string $name, $default);
    public function setParameter(string $name, $value);
}