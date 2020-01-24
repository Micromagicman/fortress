<?php

namespace fortress\util\collection;

/**
 * Вспомогательные методы для работы с массивами
 * Class ArrayUtils
 * @package fortress\util\collection
 */
class ArrayUtils {

    /**
     * Получение значения из массива по ключу, в случае отсутствия значения
     * Возвращает значение по умолчанию
     * @param array $source
     * @param $key
     * @param $defaultValue
     * @return mixed
     */
    public static function getOrDefault(array $source, $key, $defaultValue = null) {
        if (array_key_exists($key, $source)) {
            return $source[$key];
        }
        return $defaultValue;
    }
}