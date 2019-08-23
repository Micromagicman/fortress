<?php

$fortress = require_once __DIR__ . "../bootstrap.php";

$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $fortress->run($request);

$response->sendHeaders();
echo $response->getContent();
