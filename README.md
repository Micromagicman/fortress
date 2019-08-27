# Fortress framework (v0.3.1)

PHP microframework for humans

## Installation

```
composer require micromagicman/fortress
./vendor/bin/fortress
```

## New in v0.3.0

### Routing

Now you can add routes based on their http methods
```php
$routeCollection->get("welcome", "/", "app\controller\IndexController::welcome")
				->setMiddleware("app\middleware\BaseMiddleware");
```

### Command interface

Ability to run fortress via console php scripts.
Each command passed to run the framework must inherit from fortress\command\Command class.

```php
$fortress = new fortress\core\Framework();
$fortress->run(new fortress\command\CreateAppCommand());
```

### Database
Lazy database connection

### Also
- Some bug fixes & optimizations

### Environment
Initialize the environment using vendor/bin/fortress script

## Installation

```
composer require micromagicman/fortress
```

### Routing

Create config/routes.php file to define routes

```php
<?php

// Create RouteCollection factory

return function(RouteCollection $rc) {
    $rc->add("index", "/", [
        "controller" => "app\controller\IndexController",
        "action" => "main" // method name
    ]);
};
```

```php
<?php
/*
 * OR create several factories
 * To separate logically related routes
*/
return [
    function(RouteCollection $rc) {
        // Site page routes
    },
    function(RouteCollection $rc) {
        // API routes
    }
];
```

### Controllers

Any controller method must return instance of Symfony\Component\HttpFoundation\Response class

```php
<?php

namespace app\controllers;

use fortress\core\controller\Controller;

class AdminController extends Controller {

    public function index() {
        return $this->render("index");
    }

    public function login() {
        return $this->render("login");
    }

    public function register() {
        return $this->render("register");
    }

    public function auth(Authenticator $authenticator) {
        // some user auth logic
        if ($isAuthenticated) {
            return $this->redirect("admin.login"); // or "/admin/login"
        }
        return $this->redirect("admin.dashboard");
    }
}
```

There are methods of the Controller class that return an http response:

|Method   |Description   |
| ------------ | ------------ |
| render(string $templateName, array $data, int $statusCode)  | Returns response as html. **$data** parameter is required to pass parameters to the template |
| json($data, int $statusCode)  | Returns response as json string |
| redirect(string ($uri | $routeName))  | Redirects the user to **$uri**   |

### Database

Version 0.2.* supports only PostgreSQL DBMS.

#### Configuration

config/database.php

```php
<?php

return [
    "DB_DRIVER" => "pgsql",
    "DB_HOST" => "localhost",
    "DB_PORT" => 5432,
    "DB_NAME" => "db",
    "DB_USERNAME" => "user",
    "DB_PASSWORD" => "password"
];
```

```php
<?php

namespace app\service;

use fortress\core\database\DatabaseConnection;

class SomeService {

    public function doSomeStuff(DatabaseConnection $conn) {
        $users = $conn
            ->query("SELECT id, username, email FROM users");
            ->fetchAll();
        // ...
    }
}
```

## Security
Configuration user provider
You can define the users of your system directly in the config/security.php configuration file.
Thus, 2 user providers are now available:

|Provider  |Aliases   |
| ------------ | ------------ |
|fortress\security\provider\DatabaseUserProvider |db, database |
|fortress\security\provider\ConfigurationUserProvider |conf, config, configuration |

```php
// config/security.php

return [
    "user" => [
        "model" => "fortress\security\basic\BaseUser",
        "provider" => "conf" // Specify user provider
    ],
    "role" => [
        "provider" => "fortress\security\basic\BaseRoleProvider"
    ],
    "users" => [ // Create users. Each user must have email, password, role fields. Username is key of this array
        "evgen" => [
            "email" => "evgen@micromagicman.ru",
            "password" => 'someHashOfPassword',
            "role" => 62
        ]
    ]
];
```

### Authentication Errors class
A class that stores the latest authentication request errors for the next request

```php
public function auth(AuthenticationErrors $authenticationErrors) {
    if ($this->user()->is("ROLE_ADMIN")) {
        return $this->redirect("admin.app");
    }
    return $this->render("admin/login", [
        "authErrors" => $authenticationErrors->getLastErrors()
    ]);
}
```