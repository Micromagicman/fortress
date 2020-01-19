<?php

use fortress\core\configuration\Configuration;
use fortress\core\di\ContainerBuilder;
use fortress\core\Framework;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . "/../bootstrap.php";

$request = Request::createFromGlobals();
$containerBuilder = (new ContainerBuilder())
    ->withLoaders(...Configuration::configure($request))
    ->useAutowiring();

$app = new Framework($containerBuilder->build());
$response = $app->run($request);

$response->sendHeaders();
echo $response->getContent();
