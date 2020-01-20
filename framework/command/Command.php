<?php

namespace fortress\command;

abstract class Command {

    /**
     * Получение сообщения с информацией о команде
     * @return string
     */
    public function getHelpMessage() {
        $message = $this->getName() . "\n" . $this->getDescription() . "\n";
        foreach ($this->getArgumentList()->list() as $argument) {
            $message .= "\n\t-" . $argument->getInfo();
        }
        return $message;
    }

    /**
     * Список аргументов
     * По умолчанию, команда не имеет аргументов
     * @return array
     */
    public function getArgumentList() {
        return [];
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
     * Запуск команды с арегументами
     */
    public abstract function run();

    /**
     * Стандартные команды фреймворка
     * @return array: Массив имя -> класс команды
     */
    public static function getNativeCliCommands() {
        return [
            "create-app" => CreateAppCommand::class // Инициализация нового проекта
        ];
    }
}