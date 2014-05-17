<?php

namespace s2f;

require_once('s2f.class.php');
require_once('Module.class.php');
require_once('Injector.class.php');
require_once('Testor.class.php');

$core = s2f::module('core', []);

$core->service('testor', [function () {
      return new TestorServiceController();
    }]);

?>