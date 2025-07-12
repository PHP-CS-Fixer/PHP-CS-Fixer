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
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\Internal\ConfigurableFixerTemplateFixer;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect()) // @TODO 4.0 no need to call this manually
    ->setRiskyAllowed(true)
    ->registerCustomFixers([
        new ConfigurableFixerTemplateFixer(),
    ])
    ->setRules([
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,
        '@PHPUnit100Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'PhpCsFixerInternal/configurable_fixer_template' => true, // internal rules, shall not be used outside of main repo
        'general_phpdoc_annotation_remove' => ['annotations' => ['expectedDeprecation']], // one should use PHPUnit built-in method instead
        'header_comment' => ['header' => <<<'EOF'
            This file is part of PHP CS Fixer.

            (c) Fabien Potencier <fabien@symfony.com>
                Dariusz Rumiński <dariusz.ruminski@gmail.com>

            This source file is subject to the MIT license that is bundled
            with this source code in the file LICENSE.
            EOF],
        'modernize_strpos' => true, // needs PHP 8+ or polyfill
        'no_useless_concat_operator' => false, // TODO switch back on when the `src/Console/Application.php` no longer needs the concat
        'numeric_literal_separator' => true,
        'phpdoc_order' => [
            'order' => [
                'type',
                'template',
                'template-covariant',
                'template-extends',
                'extends',
                'implements',
                'property',
                'method',
                'param',
                'return',
                'var',
                'assert',
                'assert-if-false',
                'assert-if-true',
                'throws',
                'author',
                'see',
            ],
        ],
        'native_constant_invocation' => [
            'fix_built_in' => false,
            'include' => ['DIRECTORY_SEPARATOR', 'PHP_INT_SIZE', 'PHP_SAPI', 'PHP_VERSION_ID'],
        ],
    ])
    ->setFinder(
        (new Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCSIgnored(true)
            ->exclude(['dev-tools/phpstan', 'tests/Fixtures'])
            ->in(__DIR__)
            ->append([__DIR__.'/php-cs-fixer'])
    )
;
