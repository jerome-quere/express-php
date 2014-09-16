## [Module](Module) express::module(String $name, Array $dependencies) ##
This method allow you to register a new Module.

### Parameters ###
   - **$name** : The name of the new module
   - **$dependencies** : The name of all the modules require by the new module.

### Retrun value ###
The method return the instance of the new created Module

### Exemple ###
```php
$myModule = express::module('myModule', ['core']);
```
In this exemple we create a new module named *myModule* that depends on the *core* module.

---------------------------------------------------------------------------------

## [Injector](Injector) express::injector(Array $modules) ##
This method create a new injector linked with the given modules

### Parameters ###
  - **$modules** : The name of all the modules you want to link with the new injector.

### Return value ###
The method return an injector linked with the given modules.

### Exemple ###
```php
$injector = express::injector(['core']);
```
In this exemple we get an injector linked with the module core.

---------------------------------------------------------------------------------

## void express::run() ##
This method start the express-php cycle. It SHOULD be called once.

### Exemple ###
```php
express::run();
```

---------------------------------------------------------------------------------

## [Module](Module) express::getModule(String $name) ##
This method get the module with the given name

### Parameters ###
  - **$name** : The name of the module you want to get

### Return value ###
The module with the given name or null if no module is found.

### Exemple ###
```php
$module = express::getModule('core');
```
In this exemple we get the module with name 'core'