<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Component\Yaml\Yaml;

// @phpstan-ignore greaterOrEqual.alwaysFalse, booleanOr.alwaysFalse (PHPStan thinks that 80499 is max PHP version ID)
if (\PHP_VERSION_ID < 8_04_00 || \PHP_VERSION_ID >= 8_05_00) {
    fwrite(\STDERR, "PHP CS Fixer's config for PHP-HIGHEST can be executed only on highest supported PHP version - 8.4.*.\n");
    fwrite(\STDERR, "Running it on lower PHP version would prevent calling migration rules.\n");

    exit(1);
}

$config = require __DIR__.'/.php-cs-fixer.dist.php';

$phpstanConfig = Yaml::parseFile('phpstan.dist.neon');

$config->setRules(array_merge($config->getRules(), [
    '@PHP84Migration' => true,
    '@PHP82Migration:risky' => true,
    'phpdoc_to_property_type' => [ // experimental
        'types_map' => [
            'TFixerInputConfig' => 'array',
            'TFixerComputedConfig' => 'array',
            'TFixer' => '\PhpCsFixer\AbstractFixer',
        ] + $phpstanConfig['parameters']['typeAliases'],
    ],
    'fully_qualified_strict_types' => ['import_symbols' => true],
    'php_unit_attributes' => false, // as is not yet supported by PhpCsFixerInternal/configurable_fixer_template
]));

return $config;
