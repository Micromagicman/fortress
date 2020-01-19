<?php

use fortress\core\configuration\Configuration;
use fortress\core\di\ContainerBuilder;
use fortress\core\Framework;
use fortress\core\http\response\BasicResponseEmitter;
use Laminas\Diactoros\ServerRequestFactory;

require_once __DIR__ . "/../bootstrap.php";

$request = ServerRequestFactory::fromGlobals();
$containerBuilder = (new ContainerBuilder())
    ->withLoaders(...Configuration::configure($request))
    ->useAutowiring();

$app = new Framework($containerBuilder->build());
$response = $app->handleHttpRequest($request);
(new BasicResponseEmitter())->emit($response);
