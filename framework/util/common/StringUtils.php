<?php

namespace fortress\util\common;

/**
 * Class StringUtils
 * Вспомогательные функции для работы со строками
 * @package fortress\util\common
 */
class StringUtils {

    /**
     * Имеет ли строка заданный префикс
     * @param string $str
     * @param string $prefix
     * @return bool
     */
    public static function startsWith(string $str, string $prefix) {
        $prefixLen = mb_strlen($prefix);
        if ($prefixLen > mb_strlen($str)) {
            return false;
        }
        return mb_substr($str, 0, mb_strlen($prefix)) === $prefix;
    }

    /**
     * Имеет ли строка заданный суффикс
     * @param string $str
     * @param string $suffix
     * @return bool
     */
    public static function endsWith(string $str, string $suffix) {
        $mainLen = mb_strlen($str);
        $suffixLen = mb_strlen($suffix);
        if ($suffixLen > $mainLen) {
            return false;
        }
        return mb_substr($str, $mainLen - mb_strlen($suffix)) === $suffix;
    }

    /**
     * Проверка строки на пустоту
     * @param $string
     * @return bool
     */
    public static function isEmpty($string) {
        return null === $string || "" === $string;
    }

    /**
     *  Проверка строки на НЕ пустоту
     * @param $string
     * @return bool
     */
    public static function isNotEmpty($string) {
        return !self::isEmpty($string);
    }
}