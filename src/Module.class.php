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

/**
 * Express-php module class definition.
 *
 * @author Jérôme Quéré <contact@jeromequere.com>
 */
class Module
{
  private $name;
  private $dependencies;

  private $services;
  private $configHooks;
  private $runHooks;

  /**
   * Construct a new module
   * @warning This method should not be call directly use express::module instead.
   * @param name the module name
   * @param dependencies an array containing module name required by the new module.
   */
  public function __construct($name, $dependencies)
  {
    $this->name = $name;
    $this->dependencies = $dependencies;

    $this->services = array();
    $this->configHooks = array();
    $this->runHooks = array();
  }

  /**
   * Add a config hook
   * @param injectArray a callback that will be called at config time.
   * @return $this to chain methods call.
   */
  public function config($injectArray)
  {
    $this->configHooks[] = $injectArray;
    return $this;
  }

  /**
   * Add a run hook
   * @param injectArray a callback that will be called at run time.
   * @return $this to chain methods call.
   */
  public function run($injectArray)
  {
    $this->runHooks[] = $injectArray;
    return $this;
  }

  /**
   * Register a new service in the module
   * @param name the service name
   * @para $injectArray a factory method that will be called once to lazy construct the service. \
   * The methode must return the service instance.
   * @return $this to chain methods call.
   */
  public function service($name, $injectArray)
  {
    $this->services[$name] = array('instance' => null, 'factory' => $injectArray);
    return $this;
  }

  /**
   * Get a service by it's name. If the service is not yet instaciate it will call the factory method to create a new instance.
   * If no service with the given name is found it will try to find it in the dependencies modules.
   * @return the service instance or null if there is no service with the given name.
   */
  public function getService($name)
  {
    if (isset($this->services[$name]) == false)
      {
	foreach ($this->dependencies as $dep)
	  {
	    $service = $dep->getService($name);
	    if ($service)
	      return $service;
	  }
	return null;
      }

    if ($this->services[$name]['instance'] == null)
      {
	$injector = $this->getInjector();
	$this->services[$name]['instance'] = $injector->invoke($this->services[$name]['factory']);
      }
    return $this->services[$name]['instance'];
  }

  /**
   * This method will call all the registred config hook.
   * @return $this to chain methods call.
   */
  public function configHooks()
  {
    $injector = $this->getInjector();
    foreach ($this->configHooks as $hook)
      $injector->invoke($hook);
    return $this;
  }

  /**
   * This method will call all the registred run hook.
   * @return $this to chain methods call.
   */
  public function runHooks()
  {
    $injector = $this->getInjector();
    foreach ($this->runHooks as $hook)
      $injector->invoke($hook);
    return $this;
  }

  /**
   * This method return a new injector link with this module.
   * @return the injector linked with this module
   */
  private function getInjector()
  {
    return express::injector(array($this->name));
  }

}

?>