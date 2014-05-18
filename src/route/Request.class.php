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

class Request
{
  const GET = 0;
  const POST = 1;
  const PUT = 2;
  const DELETE = 3;

  private $server = array();
  private $get = array();
  private $post = array();

  public function __construct($server, $get, $post)
  {
    $this->server = $server;
    $this->get = (object)$get;
    $this->post = (object)$post;
  }

  public function getMethod()
  {
    $tmp = array("GET" => self::GET, "POST" => self::POST, "PUT" => self::PUT, "DELETE" => self::DELETE);
    if (isset($tmp[$this->server['REQUEST_METHOD']]))
      return $tmp[$this->server['REQUEST_METHOD']];
    throw new \Exception ("express::route::Request::getMethod: Unsuported method " . $this->server['REQUEST_METHOD']);
  }

  public function getPath()
  {
    $tmp = explode('?', $this->server['REQUEST_URI'], 2);
    return $tmp[0];
  }

  public function getQuery()
  {
    return $this->get;
  }

  public function getData()
  {
    return $this->post;
  }
}

?>