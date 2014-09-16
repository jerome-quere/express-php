**express-php** is a lightweight php framework with dependency injection.
Inspired by node express module and angularjs it re-uses some of their core concept.

# Requirements #
express=-php need PHP >= 5.3 and PHAR extention.

# Instalation #
Just get phar file from this address [https://raw.githubusercontent.com/jerome-quere/express-php/master/release/express.phar](https://raw.githubusercontent.com/jerome-quere/express-php/master/release/express.phar)

# Exemple #

```php
require_once('express.phar');

use express\express;

//Create the application called app and load core dependency
$app = express::module('app', ['core']);

//Register a new service factory function
$app->service('service1', function () {
    return "Service 1";
  });


//All the function registred with config will be called automaticly with injected dependency
$app->config(function ($service1) {
    echo "CONFIG with $service1\n";
  });

//All the function register with run will be called after all the config callback.
$app->run(function ($service1) {
    echo "RUN with $service1\n";
  });

//This line is the execution entry point it MUST be called after everything else is set.
express::run();

```