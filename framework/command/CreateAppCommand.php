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
        $this->writeWithData("Initialize configuration directory");
        $this->copyDirectory(
            __DIR__ . "/../../environment/config",
            getcwd() . "/config"
        );
        $this->writeWithData("Initialize public directory");
        $this->copyDirectory(
            __DIR__ . "/../../environment/public",
            getcwd() . "/public"
        );
        $this->writeWithData("Initialize templates directory");
        $this->copyDirectory(
            __DIR__ . "/../../environment/templates",
            getcwd() . "/templates"
        );
        $this->writeWithData("Initialize app directory");
        $this->copyDirectory(
            __DIR__ . "/../../environment/app",
            getcwd() . "/app"
        );
        file_put_contents(
            getcwd() . "/bootstrap.php",
            file_get_contents(__DIR__ . "/../../environment/bootstrap.php")
        );
        file_put_contents(
            getcwd() . "/commander.php",
            file_get_contents(__DIR__ . "/../../environment/commander.php")
        );
        $this->writeWithData("Update composer.json");
        $composerConfig = json_decode(file_get_contents(getcwd() . "/composer.json"), true);
        $composerConfig["autoload"]["psr-4"]["app\\"] = "app/";
        file_put_contents(
            getcwd() . "/composer.json",
            json_encode($composerConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        exec("composer install");
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