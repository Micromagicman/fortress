<?php

namespace fortress\command;

/**
 * Class CreateAppCommand
 * Команда, иницилизирующая новый проект на фреймворке Fortress.
 * Инициализирует
 *  - директорию config с файлами настроек
 *  - директорию public с файлом index.php и директориями assets/css и assets/js
 *  - директорию templates с шаблонами страниц web-приложения
 * @package fortress\cli\command
 */
class CreateAppCommand extends Command {

    /**
     * Имя команды
     * @return string
     */
    public function getName() {
        return "create-app";
    }

    /**
     * Описание команды
     * @return string
     */
    public function getDescription() {
        return "Initialize new project based on Fortress framework";
    }

    /**
     * Запуск консольной команды с арегументами
     */
    public function run() {
        echo "Initialize configuration";
        $this->copyDirectory(
            __DIR__ . "/../../environment/config",
            getcwd() . "/config"
        );
        echo "\nInitialize public dir";
        $this->copyDirectory(
            __DIR__ . "/../../environment/public",
            getcwd() . "/public"
        );
        echo "\nInitialize templates";
        $this->copyDirectory(
            __DIR__ . "/../../environment/templates",
            getcwd() . "/templates"
        );
        file_put_contents(
            getcwd() . "/bootstrap.php",
            file_get_contents(__DIR__ . "/../../environment/bootstrap.php")
        );
        echo "\nDone.";
    }

    /**
     * Рекурсивное копирование всех файлов и директорий
     * @param string $sourceDir - исходная директория
     * @param string $destinationDir - целевая директория
     */
    private function copyDirectory(string $sourceDir, string $destinationDir) {
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir);
        }
        $sourceDirResource = opendir($sourceDir);
        while ($file = readdir($sourceDirResource)) {
            if ("." !== $file && ".." !== $file) {
                if (is_dir($sourceDir . "/" . $file)) {
                    $this->copyDirectory($sourceDir . "/" . $file, $destinationDir . "/" . $file);
                } else {
                    copy(
                        $sourceDir . "/" . $file,
                        $destinationDir . "/" . $file
                    );
                }
            }
        }
        closedir($sourceDirResource);
    }
}