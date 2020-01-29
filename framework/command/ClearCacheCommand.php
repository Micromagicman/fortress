<?php


namespace fortress\command;

use fortress\cache\CacheManager;
use fortress\util\fs\FileUtils;

/**
 * Удаление директории cache
 * Class ClearCacheCommand
 * @package fortress\command
 */
class ClearCacheCommand extends Command {

    /**
     * Имя команды
     * @return string
     */
    public function getName() {
        return "clear-cache";
    }

    /**
     * Описание команды
     * @return string
     */
    public function getDescription() {
        return "Clear cache";
    }

    /**
     * Запуск команды с аргументами
     */
    public function run() {
        $this->writeWithData("Removing cache folder");
        FileUtils::removeDirectoryRecursive(CacheManager::CACHE_DIR_NAME);
        $error = error_clear_last();
        $this->writeWithData(null !== $error ? "ERROR: " . $error["message"] : "Done");
    }
}