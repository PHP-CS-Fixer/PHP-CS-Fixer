#!/usr/bin/env php
<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

Phar::mapPhar('php-cs-fixer.phar');

require_once 'phar://php-cs-fixer.phar/vendor/autoload.php';

use Symfony\CS\Console\Application;

$application = new Application();
$application->run();

__halt_compiler();
