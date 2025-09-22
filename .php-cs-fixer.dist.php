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

$fileHeaderParts = [
    <<<'EOF'
        This file is part of PHP CS Fixer.

        (c) Fabien Potencier <fabien@symfony.com>
            Dariusz Rumiński <dariusz.ruminski@gmail.com>

        EOF,
    <<<'EOF'

        This source file is subject to the MIT license that is bundled
        with this source code in the file LICENSE.
        EOF,
];

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect()) // @TODO 4.0 no need to call this manually
    ->setRiskyAllowed(true)
    ->registerCustomFixers([
        new ConfigurableFixerTemplateFixer(),
    ])
    ->setRules([
        '@PHP7x4Migration' => true,
        '@PHP7x4Migration:risky' => true,
        '@PHPUnit10x0Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'PhpCsFixerInternal/configurable_fixer_template' => true, // internal rules, shall not be used outside of main repo
        'general_phpdoc_annotation_remove' => ['annotations' => ['expectedDeprecation']], // one should use PHPUnit built-in method instead
        'header_comment' => [
            'header' => implode('', $fileHeaderParts),
            'validator' => implode('', [
                '/',
                preg_quote($fileHeaderParts[0], '/'),
                '(?P<EXTRA>.*)??',
                preg_quote($fileHeaderParts[1], '/'),
                '/s',
            ]),
        ],
        'modernize_strpos' => true, // needs PHP 8+ or polyfill
        'native_constant_invocation' => ['strict' => false], // strict:false to not remove `\` on low-end PHP versions for not-yet-known consts
        'no_useless_concat_operator' => false, // TODO switch back on when the `src/Console/Application.php` no longer needs the concat
        'numeric_literal_separator' => true,
        'phpdoc_order' => [
            'order' => [
                'deprecated',
                'final',
                'readonly',
                'internal',
                'no-named-arguments',
                'import-type',
                'type',
                'template',
                'template-covariant',
                'template-extends',
                'extends',
                'implements',
                'require-extends',
                'require-implements',
                'covers',
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
        'phpdoc_tag_no_named_arguments' => [
            'description' => 'Parameter names are not covered by the backward compatibility promise.',
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
