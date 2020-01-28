<?php

require_once "vendor/autoload.php";

use fortress\core\configuration\Configuration;
use fortress\core\di\ContainerBuilder;
use fortress\core\di\loader\MapLoader;
use fortress\core\Framework;

session_start();

$configuration = new Configuration(dirname(__FILE__));
$containerBuilder = (new ContainerBuilder())
    ->withLoaders(new MapLoader([
        Configuration::class => $configuration
    ]))
    ->withLoaders(...$configuration->configure())
    ->useAutowiring();

$app = new Framework($containerBuilder);