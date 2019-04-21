<?php

use fortress\core\Framework;
use fortress\core\configurator\Configurator;

use Symfony\Component\HttpFoundation\Request;

// Чтение конфига
$configurator = new Configurator();

// Создание объекта фреймворка
$fortress = new Framework($configurator);

// Обработка запроса
$request = Request::createFromGlobals();
$fortress->run($request);