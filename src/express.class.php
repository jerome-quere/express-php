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

/**
 * This class contains all the bases API methods.
 *
 * @author Jérôme Quéré <contact@jeromequere.com>
 */
class express
{

  /**
   * Array with all the registred modules.
   * Use module name as keys.
   */
  static private $modules = array();

  /**
   * This method register a new modules.
   * @param name the name of the module you want to register.
   * @param dependencies an array containing module name required by the new module.
   * @return the instance of the new module.
   */
  public static function module($name, $dependencies)
  {
    if (!$name) throw new InvalidArgumentException('name must be a valid string string');
    if (!is_array($dependencies)) throw new InvalidArgumentException('dependencies must be an array with module dependencies names');
    if (isset(self::$modules[$name])) throw new InvalidArgumentException("The module $name already exist");

    foreach ($dependencies as $key=>$dependency)
      {
	$module = self::getModule($dependency);
	if ($module == null)
	  throw new Exception("Could not resolve dependency $dependency");
	$dependencies[$key] = $module;
      }

    self::$modules[$name] = new Module($name, $dependencies);
    return self::$modules[$name];
  }

  /**
   * This method return a injector object that handles all the dependencies injection mechanism.
   * @param modules a list of modules name to use for the dependency injection.
   * @return an instance of an injector linked with the given modules.
   */
  public static function injector($modules)
  {
    $dependencies = array();
    foreach ($modules as $name)
      {
	if (($module = self::getModule($name)) == null)
	  throw new Exception("Could not resolve dependency $name");
	$dependencies[$name] = $module;
      }
    return new Injector($dependencies);
  }

  /**
   * This method start the express-php cycle. It SHOULD be called once.
   * @return this to chain methods call.
   */
  public static function run()
  {
    foreach (self::$modules as $module)
      $module->configHooks();
    foreach (self::$modules as $module)
      $module->runHooks();
  }

  /**
   * This method return a module with the given name.
   * @param name the name of the module you want to get.
   * @return the module with the given name or null if no mudule is found.
   */
  private static function getModule($name)
  {
    if (isset(self::$modules[$name]))
      return self::$modules[$name];
    return null;
  }
}

?>