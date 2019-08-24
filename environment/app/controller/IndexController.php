<?php

namespace app\controller;

use fortress\core\controller\Controller;
use fortress\core\http\response\HtmlResponse;

/**
 * Class IndexController
 * @package app\controller
 */
class IndexController extends Controller {
    /**
     * Render index page
     * @return HtmlResponse
     */
    public function welcome() {
        return $this->render("welcome");
    }
}