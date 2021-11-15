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

if (PHP_VERSION_ID < 70205 || PHP_VERSION_ID >= 70300) {
    fwrite(STDERR, "PHP CS Fixer's config for PHP-LOWEST can be executed only on lowest supported PHP version - ~7.2.5.\n");
    fwrite(STDERR, "Running it on higher PHP version would falsy expect more changes, eg `mixed` type on PHP 8.\n");
    exit(1);
}

$config = require '.php-cs-fixer.dist.php';

$config->getFinder()->notPath([
    // @TODO 4.0 change interface to be fully typehinted and remove the exceptions from this list
    'src/DocBlock/Annotation.php',
    'src/Tokenizer/Tokens.php',
]);

$config->setRules([
    'phpdoc_to_param_type' => true, // EXPERIMENTAL rule, helping to ensure usage of 7.0+ typing
    'phpdoc_to_return_type' => true, // EXPERIMENTAL rule, helping to ensure usage of 7.0+ typing
]);

return $config;
