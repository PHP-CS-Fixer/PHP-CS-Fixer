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
use PhpCsFixer\RuleSet\Sets\Internal\InternalRiskySet;

if (
    filter_var(getenv('PHP_CS_FIXER_TESTS_SYSTEM_UNDER_TEST'), \FILTER_VALIDATE_BOOL)
    && !filter_var(getenv('PHP_CS_FIXER_TESTS_ALLOW_ONE_TIME_SELF_CONFIG_USAGE'), \FILTER_VALIDATE_BOOL)
) {
    throw new Error(sprintf('This configuration file ("%s") is not meant to be used in tests.', __FILE__));
}

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
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRiskyAllowed(true)
    ->registerCustomRuleSets([
        new InternalRiskySet(), // available only on repo level, not exposed to external installations or phar build
    ])
    ->registerCustomFixers([
        new ConfigurableFixerTemplateFixer(), // @TODO shall be registered while registering the Set with it
    ])
    ->setRules([
        '@auto' => true,
        '@auto:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@self/internal' => true, // internal rule set, shall not be used outside of main repo
        'final_internal_class' => [
            'include' => [],
            'exclude' => ['final', 'api-extendable'],
            'consider_absent_docblock_as_internal_class' => true,
        ],
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
        'phpdoc_tag_no_named_arguments' => [
            'description' => 'Parameter names are not covered by the backward compatibility promise.',
        ],
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => [
                'arguments',
                'array_destructuring',
                'arrays',
                // 'match', // @TODO PHP 8.0: enable me
                // 'parameters', // @TODO PHP 8.0: enable me
            ],
        ],
    ])
    ->setFinder(
        (new Finder())
            ->in(__DIR__)
            ->append([__DIR__.'/php-cs-fixer'])
            ->exclude(['dev-tools/phpstan', 'tests/Fixtures'])
            ->ignoreDotFiles(false), // @TODO v4 line no longer needed
    )
;
