<?php

require_once "vendor/autoload.php";

use fortress\core\configuration\Configuration;
use fortress\core\di\ContainerBuilder;
use fortress\core\Framework;

session_start();

$containerBuilder = (new ContainerBuilder())
    ->withLoaders(...Configuration::configure())
    ->useAutowiring();

$app = new Framework($containerBuilder);