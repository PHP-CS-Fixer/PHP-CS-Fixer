<?php

return (new PhpCsFixer\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->files()
            ->in(__DIR__)
            ->name(__FILE__)
    )
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        // Overrides @PhpCsFixer's ruleset config (FQCN) using legacy fixer name.
        // This basically checks if fixers' names are normalized and legacy names can be used interchangeably with FQCNs.
        // Without this Fixer would have wanted to import `PhpCsFixer\Config` and `PhpCsFixer\Finder` used in config.
        'fully_qualified_strict_types' => [
            'import_symbols' => false,
        ],
        // Overrides @PhpCsFixer's ruleset config (FQCN) using FQCN.
        PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer::class => [
            'strategy' => 'no_multi_line',
        ],
        // Overrides @Symfony's ruleset config (legacy name) with FQCN.
        // We're disabling it here so the missing comma after last fixer is not reported.
        PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::class => false,
        // This adds new fixer not defined in the @PhpCsFixer (and nested rulesets)
        // This verifies that FQCNs used as raw strings also work as a rule name.
        'PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer' => true
    ])
;
