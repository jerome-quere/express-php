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

namespace express\route;

class Route
{
  private $method;
  private $pathPatern;
  private $pathVars;
  private $callback;

  public function __construct($method, $path, $callback)
  {
    $this->method = $method;
    $this->callback = $callback;

    $matches = array();
    $this->pathVars = array();
    if (preg_match_all('#:([a-zA-Z0-9]+)#', $path, $matches))
      {
        foreach ($matches[1] as $match)
          {
            $this->pathVars[] = $match;
          }
      }
    $this->pathPatern = '#^' .
      preg_replace('#:[a-zA-Z0-9]+#', '([a-zA-Z0-9_-]*)', $path) . '$#';
  }

  public function match($request, &$vars)
  {
    if ($this->method != null && $request->getMethod() != $this->method)
      return false;

    $matches = array();
    if (!preg_match($this->pathPatern, $request->getPath(), $matches))
      return false;

    foreach ($this->pathVars as $id=>$name)
      $vars[$name] = $matches[$id + 1];
    return true;
  }

  public function getCallback()
  {
    return $this->callback;
  }
}

class RouteProviderServiceController
{
  private $routes;
  private $defaultRoute;
  private $injector;

  public function __construct($injector)
  {
    $this->routes = array();
    $this->defaultRoute = array('response', function ($r) {$r->setCode('404')->send();});
    $this->injector = $injector;
  }

  public function get($path, $cb)
  {
    $this->when(Request::GET, $path, $cb);
  }

  public function post($path, $cb)
  {
    $this->when(Request::POST, $path, $cb);
  }

  public function put($path, $cb)
  {
    $this->when(Request::PUT, $path, $cb);
  }

  public function delete($path, $cb)
  {
    $this->when(Request::DELETE, $path, $cb);
  }

  private function when($method, $path, $cb)
  {
    $this->routes[] = new Route($method, $path, $cb);
  }

  public function otherwise($cb)
  {
    $this->defaultRoute = $cb;
  }

  private function invokeCallback($cb, $request, $response, $pathVars = array())
  {
    $this->injector->invoke($cb, array('request' => $request,
							 'response' => $response,
							 'routeParams'=> $pathVars));
  }

  public function exec($request, $response)
  {
    $pathVars = array();
    foreach ($this->routes as $route)
      {
        if ($route->match($request, $pathVars))
          {
	    $this->invokeCallback($route->getCallback(), $request, $response, (object)$pathVars);
            return;
          }
      }
    $this->invokeCallback($this->defaultRoute, $request, $response);
  }

}

?>