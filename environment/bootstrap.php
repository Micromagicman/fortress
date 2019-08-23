<?php

use fortress\core\Framework;
use fortress\core\configurator\Configurator;
use fortress\core\di\ServiceContainer;

use Symfony\Component\HttpFoundation\Request;

session_start();

// Создание объекта фреймворка
$fortress = new Framework();

// Обработка запроса
$request = Request::createFromGlobals();
$response = $fortress->run($request);

$response->sendHeaders();
echo $response->getContent();