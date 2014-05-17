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

class Module
{
  private $name;
  private $injector;
  private $services;
  private $configHooks;
  private $runHooks;

  public function __construct($name, $injector)
  {
    $this->name = $name;
    $this->injector = $injector;
    $this->services = array();
    $this->configHooks = array();
    $this->runHooks = array();

    $this->injector->addModule($this);
  }

  public function getName()
  {
    return $this->name;
  }

  public function config($injectArray)
  {
    $this->configHooks[] = $injectArray;
    return $this;
  }

  public function run($injectArray)
  {
    $this->runHooks[] = $injectArray;
  }

  public function service($name, $injectArray)
  {
    $this->services[$name] = array('instance' => null, 'factory' => $injectArray);
    return $this;
  }

  public function getService($name)
  {
    if (isset($this->services[$name]) == false)
      return null;
    if ($this->services[$name]['instance'] == null)
      $this->services[$name]['instance'] = $this->injector->invoke($this->services[$name]['factory']);
    return $this->services[$name]['instance'];
  }

  public function setInjector($injector)
  {
    $this->injector = $injector;
  }

  public function configHooks()
  {
    foreach ($this->configHooks as $hook)
      $this->injector->invoke($hook);
  }

  public function runHooks()
  {
    foreach ($this->runHooks as $hook)
      $this->injector->invoke($hook);
  }
}


?>