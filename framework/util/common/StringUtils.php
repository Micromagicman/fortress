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
        return mb_substr($str, $mainLen - mb_strlen($suffix)) === $suffix;
    }
}