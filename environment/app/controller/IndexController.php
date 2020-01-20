<?php

namespace app\controller;

use fortress\core\controller\Controller;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class IndexController
 * @package app\controller
 */
class IndexController extends Controller {

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request) {
        return $this->render("welcome");
    }
}