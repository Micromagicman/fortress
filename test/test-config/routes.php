<?php

use fortress\core\router\RouteCollection;
use test\core\ArticleCreate;

/**
 * Array of RouteCollection initializers
 */
return [
    function(RouteCollection $rc) {
        $rc->post("article.create", "/article", ArticleCreate::class);
    },
];