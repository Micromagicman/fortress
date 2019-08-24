<?php

use fortress\core\router\RouteCollection;

/**
 * Array of RouteCollection initializers
 */
return [
    function(RouteCollection $rc) {
        $rc->get("welcome", "/", "app\controller\IndexController::welcome");
    },
];