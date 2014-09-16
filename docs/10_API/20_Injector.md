## ??? Injector::invoke([Injectable](Injectable) $injectable, Array $locals = array()) ##
This method invoke the injectable given as parameters and automaticly inject services needed.

### Parameters ###
 - **$injectable** : An Injectable.
 - **$locals** *Optional*: This array can contain temporary service that will overwrite module service for the curent injection.

### Return ###
This method return the **$injectable** return value.

### Exemple ###
```php

function func1($service1, $local1) {
  echo $service1;
  echo $local1;
}

$module = express::module('myModule', []);
$module->service('service1', function () { return 42; });

$injector = express::injector('myModule');
$injector->invoke('func1', array('local1'=>84));

```
In this exemple we create a module *myModule*. We then register a service `service1` within this module. We get an injector linked to the module. The invoke statement will call the function `func1` with 42 and 84 as parameters.

-------------------------------------------------------------

## ??? Invoke::invokeMethod([Injectable](Injectable) $injectable, Array $locals = array()) ##
This method is similar to invoke except it can call object method on a given instance.

### Parameters ###
 - **$injectable** : An injectable
 - **$locals** *Optional* : This array can contain temporary service that will overwrite module service for the curent injection.

### Return ###
This method return the **$injectable** return value.

### Exemple ###
```php

class Obj
{
  private $var1;

  public function __construct($var1) {
    $this->var1 = $var1;
  }

  public function func($service1, $local1) {
    echo $this->var1;
    echo $service1;
    echo $local1;
  }
}

$module = express::module('myModule', []);
$module->service('service1', function () { return 42; });

$injector = express::injector('myModule');

$obj = new Obj(21);
$injector->invokeMethod([$obj, 'func'], array('local1'=>84));

```
```
   $> php exemple.php
   21
   42
   84
   $>
```

-------------------------------------------------------------

## ClassName Injector::instantiate(String $className, $locals = array()) ##
This method instantiate a new "className" object and inject constructor parameters.

### Parameters ###
  - **$className** : The name of the class you want to instantiate.
  - **$locals** *Optional* : This array can contain temporary service that will overwrite module service for the curent injection.

### Return ###
This method return the new instance of "className" created.

### Exemple ###
```php
class Obj
{
  private $var1;

  public function __construct($service1) {
    $this->var1 = $service1;
  }
}

$module = express::module('myModule', []);
$module->service('service1', function () { return 42; });

$injector = express::injector('myModule');
$obj = $injector->instantiate('Obj');

```