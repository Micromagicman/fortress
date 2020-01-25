<?php

use fortress\core\configuration\Configuration;
use fortress\core\di\loader\MapLoader;

return new MapLoader([
    // Database driver
    Configuration::DATABASE_DRIVER_KEY => "",
    // Database host
    Configuration::DATABASE_HOST_KEY => "",
    // Database TCP port
    Configuration::DATABASE_PORT_KEY => "",
    // Database name
    Configuration::DATABASE_NAME_KEY => "",
    // Database username
    Configuration::DATABASE_USERNAME_KEY => "",
    // Database password
    Configuration::DATABASE_PASSWORD_KEY => ""
]);