<?php

namespace fortress\util\fs;

/**
 * Class FileUtils
 * Вспомогательные функции для работы с файлами и файловой системой
 * @package fortress\util\fs
 */
class FileUtils {

    public const CURRENT_DIR = ".";
    public const PARENT_DIR = "..";

    public const EXTENSION_PHP = ".php";

    /**
     * Список всех файлов в переданной директории рекурсивно/нерекурсивно
     * @param string $rootDir
     * @param bool $recursive
     * @param array $results
     * @return array
     */
    public static function listFiles(string $rootDir, bool $recursive = false, &$results = []) {
        return self::listByCondition($rootDir, function ($file) {
            return !is_dir($file);
        }, $recursive, $results);
    }

    /**
     * Список директорий в переданной директории рекурсивно/нерекурсивно
     * В результирующий массив попадают все директории, кроме текущей (".")
     * и родительской ("..")
     * @param string $rootDir
     * @param bool $recursive
     * @param array $results
     * @return array
     */
    public static function listDirs(string $rootDir, bool $recursive = false, &$results = []) {
        return self::listByCondition($rootDir, function ($file) {
            return is_dir($file);
        }, $recursive, $results);
    }

    /**
     * Список дочерних узлов заданной директории, удовлетворяющих определенному пользователем условию
     * @param string $rootDir
     * @param callable $filterFn
     * @param bool $recursive
     * @param array $results
     * @return array
     */
    public static function listByCondition(string $rootDir, callable $filterFn, bool $recursive = false, &$results = []) {
        if (!is_dir($rootDir)) {
            return $results;
        }
        $files = scandir($rootDir);
        foreach ($files as $file) {
            $path = $rootDir . DIRECTORY_SEPARATOR . $file;
            if (self::CURRENT_DIR !== $file && self::PARENT_DIR !== $file) {
                if ($filterFn($path)) {
                    $results[] = $path;
                }
                if ($recursive && is_dir($path)) {
                    self::listByCondition($path, $filterFn, $recursive, $results);
                }
            }
        }
        return $results;
    }

    /**
     * Рекурсивное удаление директории
     * @param string $dir
     * @return bool
     */
    public static function removeDirectoryRecursive(string $dir) {
        return self::removeDirectory($dir, true);
    }

    /**
     * Удаление директории
     * @param string $dir
     * @param bool $recursive
     * @return bool
     */
    public static function removeDirectory(string $dir, bool $recursive) {
        if (!$recursive) {
            return rmdir($dir);
        }
        $dirFiles = glob($dir . '/*');
        foreach ($dirFiles as $file) {
            is_dir($file) ? self::removeDirectoryRecursive($file) : unlink($file);
        }
        return rmdir($dir);
    }
}