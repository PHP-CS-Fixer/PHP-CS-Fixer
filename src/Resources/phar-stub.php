#!/usr/bin/env php
<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (defined('HHVM_VERSION_ID')) {
    if (HHVM_VERSION_ID < 31800) {
        fwrite(STDERR, "HHVM needs to be a minimum version of HHVM 3.18.0.\n");

        if (getenv('PHP_CS_FIXER_IGNORE_ENV')) {
            fwrite(STDERR, "Ignoring environment requirements because `PHP_CS_FIXER_IGNORE_ENV` is set. Execution may be unstable.\n");
        } else {
            exit(1);
        }
    }
} elseif (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50306 || PHP_VERSION_ID >= 70200) {
    fwrite(STDERR, "PHP needs to be a minimum version of PHP 5.3.6 and maximum version of PHP 7.1.*.\n");

    if (getenv('PHP_CS_FIXER_IGNORE_ENV')) {
        fwrite(STDERR, "Ignoring environment requirements because `PHP_CS_FIXER_IGNORE_ENV` is set. Execution may be unstable.\n");
    } else {
        exit(1);
    }
}

set_error_handler(function ($severity, $message, $file, $line) {
    if ($severity & error_reporting()) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

Phar::mapPhar('php-cs-fixer.phar');

require_once 'phar://php-cs-fixer.phar/vendor/autoload.php';

use PhpCsFixer\Console\Application;

$application = new Application();
$application->run();

__HALT_COMPILER();
