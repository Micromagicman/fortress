# Fortress framework (v0.1.1)

PHP microframework for humans

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

/*
 * OR create several factories
 * To separate logically related routes
*/

<?php

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
            return $this->redirect("/login");
        }
        return $this->redirect("/admin");
    }
}
```
There are methods of the Controller class that return an http response:

|Method   |Description   |
| ------------ | ------------ |
| render(string $templateName, array $data, int $statusCode)  | Returns response as html. **$data** parameter is required to pass parameters to the template |
| json($data, int $statusCode)  | Returns response as json string |
| redirect(string $uri)  | Redirects the user to **$url**   |

### Database

Version 0.1.* supports only PostgreSQL DBMS.

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
        $usersStatement = $conn->query("SELECT id, username, email FROM users");
        $users = $usersStatement->fetchAll();
        // ...
    }
}

```