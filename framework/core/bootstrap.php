<?php

use fortress\core\Framework;
use fortress\core\configurator\Configurator;
use fortress\core\di\ServiceContainer;

use Symfony\Component\HttpFoundation\Request;

// Чтение конфига
$configurator = new Configurator();
$container = new ServiceContainer();

// Создание объекта фреймворка
$fortress = new Framework($configurator, $container);

// Обработка запроса
$request = Request::createFromGlobals();
$fortress->run($request);