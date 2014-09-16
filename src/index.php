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

require_once('express.class.php');
require_once('Module.class.php');
require_once('Injector.class.php');

$core = express::module('core', []);

?>