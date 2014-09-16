## [Module](#) Module::config([Injectable](./Injectable) $injectable) ##
This methode register a new config hook on a module. Config hook are method that are executed when all the module are ready but before run hooks.

### Parameters ###
  - **$injectable** : An injectable

### Return ###
Return the current module

### Exemple ###
```php

function config1()
{
  echo "Config1";
}

$module = express::module("myModule", ['core'])

$module->config('config1')->config(function () {
  echo "Config2";
});

express::run();
```
In this exemple we create a module named *myModule* and register 2 config hook that respectively pring `config1` and `config2`.

-------------------------------------------------------------------------------

## [Module](#) Module::run([Injectable](./Injectable) $injectable) ##
This methode register a new run hook on a module. Run hook are method that are executed when all the module are ready and after all config hooks.

### Parameters ###
  - **$injectable** : An injectable

### Return ###
Return the current module

### Exemple ###
```php

function run()
{
  echo "run";
}

function config()
{
  echo "config";
}

$module = express::module("myModule", ['core'])

$module->config('config')->run('run');
});

express::run();
```
In this exemple we create a module named *myModule* and register 1 config hook and 1 run hook that respectively pring `config` and `run`. The execution will first exec config hook and then run hook when `express::run()` is called.

-------------------------------------------------------------------------------

## [Module](#) Module::service(String $name, [Injectable](Injectable) $injectable) ##
This method register a new service in the module.

### Parameters ###
  - **$name** : The name of the new service.
  - **$injectable** : an injectable that MUST create AND retrun an instance of the service.

### Exemple ###
```php

class Service1
{
  public function getNumber() { return 42; }
}

$module = express::module('myModule', []);
$module->service('service1', function () {
  return new Service1();
});

$module->run(function ($service1) {
  echo $service1->getNumber();
});

express:run();

```
In this exemple we create a module *myModule* then register a service named *service1*. Finnaly we add a run hook that will automaticly be called with the service1 instance.