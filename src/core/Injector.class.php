<?php
/*
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Jerome Quere
 *
 * Permission is hereby granted, free of charge, to any person obtaining a  copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including  without limitation the rights
 * to use, copy, modify, merge, publish,  distribute,  sublicense,  and/or  sell
 * copies  of  the  Software,  and  to  permit  persons  to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above  copyright  notice  and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS  PROVIDED "AS IS", WITHOUT  WARRANTY  OF ANY KIND, EXPRESS OR
 * IMPLIED,  INCLUDING  BUT NOT  LIMITED  TO THE  WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN  NO EVENT SHALL THE
 * AUTHORS OR  COPYRIGHT  HOLDERS  BE  LIABLE  FOR A NY CLAIM,  DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace s2f;
use \Exception as Exception;
use \ReflectionFunction as ReflectionFunction;
use \ReflectionClass as ReflectionClass;
use \ReflectionMethod as ReflectionMethod;

class Injector
{
  private $deps;

  public function __construct($deps)
  {
    $this->deps = $deps;
  }

  public function addModule($module)
  {
    $this->deps[$module->getName()] = $module;
  }

  public function invoke($injectArray, $locals = array())
  {
    if (is_callable($injectArray))
      $injectArray = $this->buildInjectArrayFromFunction($injectArray);

    $fct = array_pop($injectArray);
    foreach ($injectArray as $idx=>$value)
      {
	$injectArray[$idx] = $this->getService($value, $locals);
      }
    return call_user_func_array($fct, $injectArray);
  }

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

  public function instantiate($classname, $locals)
  {
    return $this->invoke($this->buildInjectArrayFromClassName($classname), $locals);
  }

  private function buildInjectArrayFromFunction($fn)
  {
    $reflexion = new ReflectionFunction($fn);
    $result = array();
    foreach ($reflexion->getParameters() as $param)
      $result[] = $param->name;
    $result[] = $fn;
    return $result;
  }

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

  private function buildInjectArrayFromClassName($className)
  {
    $result = array();
    $reflexion = new ReflectionClass($className);
    if (isset($className::$injector))
      {
	$result = $className::$injector;
      }
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

  private function getService($name, $locals)
  {
    if (isset($locals[$name]))
      return $locals[$name];
    if ($name == 'injector')
      return $this;

    foreach ($this->deps as $n=>$module)
      {
	$service = $module->getService($name);
	if ($service)
	  return $service;
      }
    throw new Exception(sprintf("Can't load service with name %s", $name));
  }
}

?>