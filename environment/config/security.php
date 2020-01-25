<?php

return [
    "user" => [
        "model" => "fortress\security\basic\BaseUser",
        "provider" => "db"
    ],
    "role" => [
        "provider" => "fortress\security\basic\BaseRoleProvider"
    ],
    "users" => [
    ]
];