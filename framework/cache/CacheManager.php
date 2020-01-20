<?php

namespace fortress\cache;

abstract class CacheManager {

    public const CACHE_DIR_NAME = "cache";

    protected function __construct() {
        if ($this->isRunningFromCli() && !is_dir(self::CACHE_DIR_NAME)) {
            mkdir(self::CACHE_DIR_NAME);
        }
    }

    /**
     * Проверка на запуск скрипта из командной строки
     * @return bool
     */
    protected function isRunningFromCli() {
        return "cli" === php_sapi_name();
    }

    /**
     * Сохранение данных в файл
     * @param $data
     * @return mixed
     */
    public abstract function save($data);

    /**
     * Восстановление данных из файла кеша
     * @return mixed
     */
    public abstract function restore();

    /**
     * Имя файла кеша
     * @return string
     */
    protected abstract function targetFileName();
}