<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (PHP_VERSION_ID < 80100 || PHP_VERSION_ID >= 80200) {
    fwrite(STDERR, "PHP CS Fixer's config for PHP-HIGHEST can be executed only on highest supported PHP version - 8.1.*.\n");
    fwrite(STDERR, "Running it on lower PHP version would prevent calling migration rules.\n");
    exit(1);
}

$config = require '.php-cs-fixer.dist.php';

$config->setRules(array_merge($config->getRules(), [
    '@PHP81Migration' => true,
    '@PHP80Migration:risky' => true,
    'heredoc_indentation' => false,
]));

return $config;
