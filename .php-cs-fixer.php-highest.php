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

// @phpstan-ignore greaterOrEqual.alwaysFalse, booleanOr.alwaysFalse (PHPStan thinks that 80499 is max PHP version ID)
if (\PHP_VERSION_ID < 8_04_00 || \PHP_VERSION_ID >= 8_05_00) {
    fwrite(\STDERR, "PHP CS Fixer's config for PHP-HIGHEST can be executed only on highest supported PHP version - 8.4.*.\n");
    fwrite(\STDERR, "Running it on lower PHP version would prevent calling migration rules.\n");

    exit(1);
}

$config = require __DIR__.'/.php-cs-fixer.dist.php';

$config->getFinder()->notPath([
    'src/Tokenizer/Tokens.php', // due to some quirks on SplFixedArray typing
]);

$typesMap = [
    'T' => 'mixed',
    'TFixerInputConfig' => 'array',
    'TFixerComputedConfig' => 'array',
    'TFixer' => '\PhpCsFixer\AbstractFixer',
    '_PhpTokenKind' => 'int|string',
    '_PhpTokenArray' => 'array{0: int, 1: string}',
    '_PhpTokenArrayPartial' => 'array{0: int, 1?: string}',
    '_PhpTokenPrototype' => '_PhpTokenArray|string',
    '_PhpTokenPrototypePartial' => '_PhpTokenArrayPartial|string',
];

$config->setRules(array_merge($config->getRules(), [
    '@PHP8x4Migration' => true,
    '@PHP8x2Migration:risky' => true,
    'phpdoc_to_param_type' => ['types_map' => $typesMap],
    'phpdoc_to_return_type' => ['types_map' => $typesMap],
    'phpdoc_to_property_type' => ['types_map' => $typesMap],
    'fully_qualified_strict_types' => ['import_symbols' => true],
    'php_unit_attributes' => false, // as is not yet supported by PhpCsFixerInternal/configurable_fixer_template
]));

return $config;
