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

use PhpCsFixer\Config;
use PhpCsFixer\Future;

// @phpstan-ignore greaterOrEqual.alwaysFalse, booleanOr.alwaysFalse (PHPStan thinks that 80499 is max PHP version ID)
if (\PHP_VERSION_ID < 8_05_00 || \PHP_VERSION_ID >= 8_06_00) {
    fwrite(\STDERR, "PHP CS Fixer's config for PHP-HIGHEST can be executed only on highest supported PHP version - 8.5.*.\n");
    fwrite(\STDERR, "Running it on lower PHP version would prevent calling migration rules.\n");

    exit(1);
}

$config = require __DIR__.'/.php-cs-fixer.dist.php';

Closure::bind(
    static function (Config $config): void { $config->name = 'PHP-HIGHEST'.(Future::isFutureModeEnabled() ? ' (future mode)' : ''); },
    null,
    Config::class,
)($config);

$config->getFinder()->notPath([
    'src/Doctrine/Annotation/Tokens.php', // due to some quirks on SplFixedArray typing
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
    '@PHP8x5Migration' => true,
    '@PHP8x5Migration:risky' => true,
    'phpdoc_to_param_type' => ['types_map' => $typesMap],
    'phpdoc_to_return_type' => ['types_map' => $typesMap],
    'phpdoc_to_property_type' => ['types_map' => $typesMap],
    'fully_qualified_strict_types' => ['import_symbols' => true],
    'php_unit_attributes' => false, // as is not yet supported by PhpCsFixerInternal/configurable_fixer_template
]));

return $config;
