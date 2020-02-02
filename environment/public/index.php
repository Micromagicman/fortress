<?php

use fortress\core\http\response\BasicResponseEmitter;
use Laminas\Diactoros\ServerRequestFactory;

require_once __DIR__ . "/../bootstrap.php";

session_start();

$request = ServerRequestFactory::fromGlobals();
$response = $app->handleHttpRequest($request);
(new BasicResponseEmitter())->emit($response);
