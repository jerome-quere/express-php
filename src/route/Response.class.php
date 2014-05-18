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

class Response
{
  private $code = 200;
  private $headers = array();
  private $isHeaderWrite = false;

  public function __construct()
  {
  }

  public function setCode($code)
  {
    $this->code = $code;
    return $this;
  }

  public function setHeader($var, $value = null)
  {
    if ($value == null)
      {
	foreach ($var as $key=>$value)
	  $this->setHeader($key, $value);
      }
    else
      $this->headers[$var] = $value;
    return $this;
  }

  public function setType($mime)
  {
    $this->setHeader('Content-Type', $mime);
  }

  public function sendJson($array)
  {
    $this->setType(Mime::get(Mime::JSON));
    $this->send(json_encode($array));
  }

  public function send($data = '')
  {
    if ($this->isHeaderWrite == false)
      {
	$this->writeHeader();
	$this->isHeaderWrite = true;
      }
    echo $data;
  }

  private function writeHeader()
  {
    http_response_code($this->code);
    foreach ($this->headers as $key=>$value)
      header("$key: $value", true);
  }
}

?>