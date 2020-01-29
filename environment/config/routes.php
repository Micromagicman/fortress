<?php

use app\controller\IndexController;
use fortress\core\router\RouteCollection;

/**
 * Array of RouteCollection initializers
 */
return [
    function(RouteCollection $rc) {
        $rc->get("welcome", "/", IndexController::class);
    },
];