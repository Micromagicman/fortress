<?php

namespace fortress\core\controller;

use fortress\core\exception\FortressException;

/**
 * Ошибка, связанная с некорректным типом возвращаемого значения из Action-ов
 * в процессе обработки HTTP-запроса
 * Class UnexpectedResponseException
 * @package fortress\core\controller
 */
class UnexpectedResponseException extends FortressException {
    public function __construct($message = "") {
        parent::__construct($message);
    }
}