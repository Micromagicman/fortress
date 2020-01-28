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

    /**
     * Вычисление значения при отсутсвии по заданному ключу
     * Вычисленное значение попадает в переданный массив по заданному ключу
     * а также возращается вызвавшешму данный метод коду
     * @param array $source
     * @param $key
     * @param callable $computer
     * @return mixed
     */
    public static function computeIfNotPresent(array $source, $key, callable $computer) {
        if (array_key_exists($key, $source)) {
            return $source[$key];
        }
        $value = $computer($key);
        $source[$key] = $value;
        return $value;
    }
}