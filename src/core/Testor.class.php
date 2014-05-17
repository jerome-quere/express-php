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

class Testor
{
  private $value;
  private $exception;

  public function __construct($var, $exception = null)
  {
    $this->value = $var;
    $this->exception = ($exception) ? $exception : new \Exception("s2f::Testor: Test failed for value " . $this->value);
  }

  public function getValue() { return $this->value; }

  private function testEmpty($v)   { return $v == false; }
  private function testNull($v)    { return $v === null; }
  private function testTrue($v)    { return $v === true; }
  private function testString($v)  { return is_string($v); }
  private function testNumeric($v) { return is_numeric($v); }
  private function test($v, $fn)   { return $fn($v); }

  public function __call($name, $argument)
  {
    array_unshift($argument, $this->value);
    if (strncmp($name, "isNot", 5) == 0)
      {
	$name = "test" . substr($name, 5);
	if (call_user_func_array(array($this, $name), $argument) !== false)
	  throw $this->exception;
      }
    else if (strncmp($name, "is", 2) == 0)
      {
	$name = "test" . substr($name, 2);
	if (call_user_func_array(array($this, $name), $argument) !== true)
	  throw $this->exception;
      }
    else
      throw new \Exception("Testor: Unsupported Method " . $name);
    return $this;
  }
}

class TestorServiceController
{
  public function __construct()
  {

  }

  public function __invoke($var, $exception = null)
  {
    return new Testor($var, $exception);
  }

}

?>