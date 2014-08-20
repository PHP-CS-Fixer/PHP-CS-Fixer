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

if (version_compare(phpversion(), '5.3.6', '<')) {
    fwrite(STDERR, "PHP needs to be a minimum version of PHP 5.3.6\n");
    exit(1);
}

Phar::mapPhar('php-cs-fixer.phar');

require_once 'phar://php-cs-fixer.phar/vendor/autoload.php';

use Symfony\CS\Console\Application;

$application = new Application();
$application->run();

__HALT_COMPILER();
