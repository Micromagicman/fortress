<?php

use fortress\core\Framework;
use fortress\core\configurator\Configurator;
use fortress\core\di\ServiceContainer;

use Symfony\Component\HttpFoundation\Request;

session_start();

// Чтение конфига
$configurator = new Configurator();
$container = new ServiceContainer();

// Создание объекта фреймворка
$fortress = new Framework($configurator, $container);

// Обработка запроса
$request = Request::createFromGlobals();
$response = $fortress->run($request);

$response->sendHeaders();
echo $response->getContent();