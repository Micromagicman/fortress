<?php

use fortress\command\Command;

$args = array_slice($argv, 1);
if (empty($args)) {
    die("Command name required");
}

require_once __DIR__ . "/bootstrap.php";

/**
 * Проверка существования команды с переданным именем
 * На данный момент (v0.4.*), пользователь не может создавать свои команды
 * Все доступные для запуска команды есть в пакете fortress\command
 */
$commandName = $args[0];
$commands = Command::getNativeCliCommands();
if (!array_key_exists($commandName, $commands)) {
    die("Command '$commandName' not exists'");
}

/**
 * Создание и запуск команды с помощью cli-интерфейса fortress
 */
$commandClass = $commands[$commandName];
$command = new $commandClass();
/** @var Command $command */
$command->setArguments(array_slice($args, 1));
$app->handleCommand($command);