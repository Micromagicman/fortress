<?php

namespace fortress\command;

abstract class Command {

    protected array $arguments;

    /**
     * Вывод строки с текущим временем в stdOut
     * @param string $line
     */
    protected function writeWithData(string $line) {
        echo "[" . date("Y-m-d, H:i") . "] " . $line . "\n";
    }

    /**
     * Получение сообщения с информацией о команде
     * @return string
     */
    public function getHelpMessage() {
        $message = $this->getName() . "\n" . $this->getDescription() . "\n";
        foreach ($this->getParameters() as $argument) {
            $message .= "\n\t-" . $argument->getInfo();
        }
        return $message;
    }

    /**
     * Передача параметров командной строки
     * @param array $arguments
     */
    public function setArguments(array $arguments) {
        $this->arguments = $arguments;
    }

    /**
     * Список парметров
     * По умолчанию, команда не имеет параметров
     * @return array
     */
    public function getParameters() {
        return [];
    }

    /**
     * Список аргументов
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Имя команды
     * @return string
     */
    public abstract function getName();

    /**
     * Описание команды
     * @return string
     */
    public abstract function getDescription();

    /**
     * Запуск команды с аргументами
     */
    public abstract function run();

    /**
     * Стандартные команды фреймворка
     * @return array: Массив имя -> класс команды
     */
    public static function getNativeCliCommands() {
        return [
            "create-app" => CreateAppCommand::class, // Инициализация нового проекта
            "create-routes" => CreateRoutesCommand::class, // Парсинг маршрутов из аннотаций методов контроллеров
            "clear-cache" => ClearCacheCommand::class // Очистка кеша
        ];
    }
}