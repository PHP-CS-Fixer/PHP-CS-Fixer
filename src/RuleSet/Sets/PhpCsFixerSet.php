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

namespace PhpCsFixer\RuleSet\Sets;

use PhpCsFixer\Fixer;
use PhpCsFixer\RuleSet\AbstractRuleSetDescription;
use PhpCsFixer\RuleSet\RuleSet;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpCsFixerSet extends AbstractRuleSetDescription
{
    public function getRules(): array
    {
        return RuleSet::normalizeConfig([
            '@PER-CS' => true,
            '@Symfony' => true,
            Fixer\Whitespace\BlankLineBeforeStatementFixer::class => [
                'statements' => [
                    'break',
                    'case',
                    'continue',
                    'declare',
                    'default',
                    'exit',
                    'goto',
                    'include',
                    'include_once',
                    'phpdoc',
                    'require',
                    'require_once',
                    'return',
                    'switch',
                    'throw',
                    'try',
                    'yield',
                    'yield_from',
                ],
            ],
            Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer::class => true,
            Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer::class => true,
            Fixer\ControlStructure\EmptyLoopBodyFixer::class => true,
            Fixer\LanguageConstruct\ExplicitIndirectVariableFixer::class => true,
            Fixer\StringNotation\ExplicitStringVariableFixer::class => true,
            Fixer\Import\FullyQualifiedStrictTypesFixer::class => [
                'import_symbols' => true,
            ],
            Fixer\StringNotation\HeredocToNowdocFixer::class => true,
            Fixer\FunctionNotation\MethodArgumentSpaceFixer::class => [
                'after_heredoc' => true,
                'on_multiline' => 'ensure_fully_multiline',
            ],
            Fixer\Whitespace\MethodChainingIndentationFixer::class => true,
            Fixer\Comment\MultilineCommentOpeningClosingFixer::class => true,
            Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer::class => [
                'strategy' => 'new_line_for_chained_calls',
            ],
            Fixer\Whitespace\NoExtraBlankLinesFixer::class => [
                'tokens' => [
                    'attribute',
                    'break',
                    'case',
                    'continue',
                    'curly_brace_block',
                    'default',
                    'extra',
                    'parenthesis_brace_block',
                    'return',
                    'square_brace_block',
                    'switch',
                    'throw',
                    'use',
                ],
            ],
            Fixer\ControlStructure\NoSuperfluousElseifFixer::class => true,
            Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer::class => [
                'allow_hidden_params' => true,
                'allow_mixed' => true,
                'remove_inheritdoc' => true,
            ],
            Fixer\ControlStructure\NoUnneededControlParenthesesFixer::class => [
                'statements' => [
                    'break',
                    'clone',
                    'continue',
                    'echo_print',
                    'negative_instanceof',
                    'others',
                    'return',
                    'switch_case',
                    'yield',
                    'yield_from',
                ],
            ],
            Fixer\ControlStructure\NoUselessElseFixer::class => true,
            Fixer\ReturnNotation\NoUselessReturnFixer::class => true,
            Fixer\Operator\OperatorLinebreakFixer::class => true,
            Fixer\ClassNotation\OrderedClassElementsFixer::class => true,
            Fixer\ClassNotation\OrderedTypesFixer::class => [
                'null_adjustment' => 'always_last',
            ],
            Fixer\PhpUnit\PhpUnitDataProviderMethodOrderFixer::class => true,
            Fixer\PhpUnit\PhpUnitInternalClassFixer::class => true,
            Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer::class => true,
            Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer::class => true,
            Fixer\Phpdoc\PhpdocNoEmptyReturnFixer::class => true,
            Fixer\Phpdoc\PhpdocOrderByValueFixer::class => true,
            Fixer\Phpdoc\PhpdocTypesOrderFixer::class => true,
            Fixer\ClassNotation\ProtectedToPrivateFixer::class => true,
            Fixer\ReturnNotation\ReturnAssignmentFixer::class => true,
            Fixer\ClassNotation\SelfStaticAccessorFixer::class => true,
            Fixer\Comment\SingleLineCommentStyleFixer::class => true,
            Fixer\Basic\SingleLineEmptyBodyFixer::class => true,
            Fixer\FunctionNotation\SingleLineThrowFixer::class => false,
            Fixer\StringNotation\StringImplicitBackslashesFixer::class => true,
            Fixer\ControlStructure\TrailingCommaInMultilineFixer::class => ['after_heredoc' => true, 'elements' => ['array_destructuring', 'arrays']],
            Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer::class => ['ensure_single_space' => true],
        ]);
    }

    public function getDescription(): string
    {
        return 'Rule set as used by the PHP CS Fixer development team, highly opinionated.';
    }
}
