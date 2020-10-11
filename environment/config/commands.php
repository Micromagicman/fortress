<?php

use App\command\CommandCommand;
use fortress\core\di\loader\MapLoader;

return new MapLoader([
    "command" => CommandCommand::class
]);