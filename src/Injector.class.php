<?php
/*
 * This file is part of the express-php package.
 *
 * (c) Jérôme Quéré <contact@jeromequere.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace express;
use \Exception as Exception;
use \ReflectionFunction as ReflectionFunction;
use \ReflectionClass as ReflectionClass;
use \ReflectionMethod as ReflectionMethod;

/**
 * This class handle all the dependency injection system.
 *
 * @author Jérôme Quéré <contact@jeromequere.com>
 */
class Injector
{
  /**
   * An array of modules linked with this injector instance.
   * Modules names are used as keys.
   */
  private $modules;

  /**
   * This method construct a new injector object.
   * @warning This method should not be called use express::injector instead.
   */
  public function __construct($modules)
  {
    $this->modules = $modules;
  }

  /**
   * This method invoke the given method and inject services as dependencies.
   * @param injectArray can be a function name, a function name or an injectArray
   * @param locals add new temporary services that can be inject in the given method.
   * @return the result of the called function.
   */
  public function invoke($injectArray, $locals = array())
  {
    if (is_callable($injectArray))
      $injectArray = $this->buildInjectArrayFromFunction($injectArray);

    $fct = array_pop($injectArray);
    foreach ($injectArray as $idx => $serviceName)
      $injectArray[$idx] = $this->get($serviceName, $locals);

    return call_user_func_array($fct, $injectArray);
  }

  /**
   * Same as invoke except this one will invoke a methode on a specific object.
   * @param injectArray an array of size two. First the instance of the object and then the method name.
   * @param locals add new temporary services that can be inject in the given method.
   * @return the result of the called method.
   */
  public function invokeMethod($injectArray, $locals = array())
  {
    if (sizeof($injectArray) == 2 && is_object($injectArray[0]))
      $injectArray = $this->buildInjectArrayFromMethod($injectArray[0], $injectArray[1]);

    $method = array_pop($injectArray);
    $instance = array_pop($injectArray);
    $injectArray[] = function () use ($instance, $method) {
      return call_user_func_array(array($instance, $method), func_get_args());
    };
    return $this->invoke($injectArray, $locals);
  }

  /**
   * Create a new instance of the object with the given classname injecting service as consctructor parameters.
   * @param classname the classname of the object you want to instantiate.
   * @param locals add new temporary services that can be inject in the given method.
   * @return the instance of classname created.
   */
  public function instantiate($classname, $locals)
  {
    return $this->invoke($this->buildInjectArrayFromClassName($classname), $locals);
  }

  /**
   * Create an injectArray from a given function
   */
  private function buildInjectArrayFromFunction($fn)
  {
    $reflexion = new ReflectionFunction($fn);
    $result = array();
    foreach ($reflexion->getParameters() as $param)
      $result[] = $param->name;
    $result[] = $fn;
    return $result;
  }

  /**
   * Create an injectArray for a given method and instance
   */
  private function buildInjectArrayFromMethod($instance, $method)
  {
    $reflexion = new ReflectionMethod($instance, $method);
    $result = array();
    foreach ($reflexion->getParameters() as $param)
      $result[] = $param->name;
    $result[] = $instance;
    $result[] = $method;
    return $result;
  }

  /**
   * Create an injectArray from a given classname constructor
   */
  private function buildInjectArrayFromClassName($className)
  {
    $result = array();
    $reflexion = new ReflectionClass($className);

    if (isset($className::$injector))
      $result = $className::$injector;
    else
      {
	$constructor = $reflexion->getConstructor();
	foreach ($constructor->getParameters() as $param)
	  $result[] = $param->name;
      }

    $result[] = function () use ($reflexion) {
      return $reflexion->newInstanceArgs(func_get_args());
    };
    return $result;
  }

  /**
   * Return a service with the given name.
   * If a locals exist with the givent name it will be return.
   * @param name the name of the service you want to get
   * @param locals array of temporary services that temporary overwrite module services.
   * @throw This method will throw if no service can be found with the given name
   * @return the instance of the service with the given name.
   */
  public function get($name, $locals = array())
  {
    if (isset($locals[$name]))
      return $locals[$name];
    if ($name == 'injector')
      return $this;

    foreach ($this->modules as $n=>$module)
      {
	$service = $module->getService($name);
	if ($service)
	  return $service;
      }
    throw new Exception(sprintf("Can't load service with name %s", $name));
  }
}

?>