<?php

require_once "bootstrap.php";

error_reporting(E_ERROR | E_PARSE);

$args = array_slice($argv, 1);
if (empty($args)) {
    die("Command name required");
}

/**
 * Проверка существования команды с переданным именем
 */
$commandName = $args[0];
$app->handleCommand($commandName, array_slice($args, 1));
