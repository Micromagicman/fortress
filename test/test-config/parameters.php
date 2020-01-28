<?php

use fortress\core\di\loader\MapLoader;

return new MapLoader([
    "template.404" => "404",
    "template.dir" => ".." . DIRECTORY_SEPARATOR . "templates",
    "template.type" => "php"
]);