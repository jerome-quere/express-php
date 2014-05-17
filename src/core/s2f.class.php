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

class s2f
{
  static private $modules = array();

  public static function module($name, $dps)
  {
    self::$modules[$name] = new Module($name, self::injector($dps));
    return self::$modules[$name];
  }

  public static function injector($deps)
  {
    $dependencies = array();
    foreach ($deps as $dp_name)
      {
	if (($module = self::getModule($dp_name)) == null)
	  throw new Exception(sprintf("Could not resolve dependency %s", $dp_name));
	$dependencies[$dp_name] = $module;
      }
    return new Injector($dependencies);
  }

  public static function run()
  {
    foreach (self::$modules as $module)
      $module->configHooks();
    foreach (self::$modules as $module)
      $module->runHooks();
  }

  private static function getModule($name)
  {
    if (isset(self::$modules[$name]))
      return self::$modules[$name];
    return null;
  }
}

?>