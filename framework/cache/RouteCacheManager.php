<?php

namespace fortress\cache;

use fortress\util\fs\FileUtils;

class RouteCacheManager extends CacheManager {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Сохранение данных в файл
     * @param $data
     * @return mixed
     */
    public function save($data) {
        file_put_contents($this->targetFileName(), serialize($data));
    }

    /**
     * Восстановление данных из файла кеша
     * @return mixed
     */
    public function restore() {
        $cacheFile = $this->targetFileName();
        if (!file_exists($cacheFile)) {
            return [];
        }
        $serializedRoutes = file_get_contents($cacheFile);
        return unserialize($serializedRoutes);
    }

    /**
     * Имя файла кеша
     * @return string
     */
    protected function targetFileName() {
        $cacheDir = self::CACHE_DIR_NAME . DIRECTORY_SEPARATOR . "cache_routes";
        return $this->isRunningFromCli()
            ? $cacheDir
            : FileUtils::PARENT_DIR . DIRECTORY_SEPARATOR . $cacheDir;
    }
}