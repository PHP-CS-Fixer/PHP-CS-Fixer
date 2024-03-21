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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\ControlCaseStructuresAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Vincent Langlet
 */
final class SingleExpressionPerLineFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    public const ELEMENTS_ARRAYS = 'arrays';

    /**
     * @internal
     */
    public const ELEMENTS_ARGUMENTS = 'arguments';

    /**
     * @internal
     */
    public const ELEMENTS_PARAMETERS = 'parameters';

    /**
     * @internal
     */
    public const ELEMENTS_CONTROL_STRUCTURES = 'control_structures';

    /**
     * @internal
     */
    public const SWITCH_CASES = 'case';

    /**
     * @internal
     */
    public const MATCH_EXPRESSIONS = 'match';

    private ?array $switchColonIndexes = null;

    private bool $fixArrays = false;
    private bool $fixArguments = false;
    private bool $fixParameters = false;
    private bool $fixControlStructures = false;
    private bool $fixSwitchCases = false;
    private bool $fixMatch = false;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Multi-line arrays, arguments list, parameters list, control structures, `switch` cases and `match` expressions should have one element by line.',
            [
                new CodeSample("<?php\narray(1,\n    2);\n"),
                new CodeSample("<?php\nfoo(1,\n    2);\n", ['elements' => [self::ELEMENTS_ARGUMENTS]]),
                new CodeSample("<?php\nif (\$a\n    && \$b) {};\n", ['elements' => [self::ELEMENTS_CONTROL_STRUCTURES]]),
                new CodeSample("<?php\nswitch (\$foo) {\n    case 0: case 1:\n        return null;\n    };\n", ['elements' => [self::SWITCH_CASES]]),
                new CodeSample("<?php\nfunction foo(\$x,\n    \$y)\n{\n}\n", ['elements' => [self::ELEMENTS_PARAMETERS]]),
                new VersionSpecificCodeSample("<?php\nmatch(\$x) {\n    1 => 1, 2 => 2\n};\n", new VersionSpecification(8_00_00), ['elements' => [self::MATCH_EXPRESSIONS]]),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ArrayIndentationFixer, StatementIndentationFixer.
     * Must run after MethodArgumentSpaceFixer.
     */
    public function getPriority(): int
    {
        return 29;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN, '(']);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('elements', 'Which expression must have one element by line.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset([
                    self::ELEMENTS_ARRAYS,
                    self::ELEMENTS_ARGUMENTS,
                    self::ELEMENTS_PARAMETERS,
                    self::ELEMENTS_CONTROL_STRUCTURES,
                    self::SWITCH_CASES,
                    self::MATCH_EXPRESSIONS,
                ])])
                ->setDefault([
                    self::ELEMENTS_ARRAYS,
                    self::ELEMENTS_ARGUMENTS,
                    self::ELEMENTS_PARAMETERS,
                    self::ELEMENTS_CONTROL_STRUCTURES,
                    self::SWITCH_CASES,
                    self::MATCH_EXPRESSIONS,
                ])
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->fixArrays = \in_array(self::ELEMENTS_ARRAYS, $this->configuration['elements'], true);
        $this->fixArguments = \in_array(self::ELEMENTS_ARGUMENTS, $this->configuration['elements'], true);
        $this->fixParameters = \in_array(self::ELEMENTS_PARAMETERS, $this->configuration['elements'], true);
        $this->fixControlStructures = \in_array(self::ELEMENTS_CONTROL_STRUCTURES, $this->configuration['elements'], true);
        $this->fixSwitchCases = \in_array(self::SWITCH_CASES, $this->configuration['elements'], true);
        $this->fixMatch = \in_array(self::MATCH_EXPRESSIONS, $this->configuration['elements'], true);

        $this->processBlock($tokens, 0, $tokens->count() - 1, false);
    }

    /**
     * Process tokens from $tokens[$begin] to $tokens[$end] and add missing new lines:
     * - If the code is not multiline, there is nothing to add
     * - If the code is multiline, it looks for newline right after the first token,
     * after every comma, and right before the last token and add them when they are missing.
     *
     * Since recursion is used to handle things like array inside array or functions we need
     * a parameter $shouldAddNewLine to knows if we are currently fixing the structure, which
     * requires to add newlines, or if we are just looking for the next one to fix.
     *
     * @param null|non-empty-array<string> $expressionDelimiters
     * @param null|non-empty-array<int>    $expressionDelimitersIndexes
     */
    private function processBlock(
        Tokens $tokens,
        int $begin,
        int $end,
        bool $shouldAddNewLine,
        bool $enforceOneExpressionPerLine = false,
        ?array $expressionDelimiters = null,
        ?array $expressionDelimitersIndexes = null

    ): int {
        if (!$tokens->isPartialCodeMultiline($begin, $end)) {
            return 0;
        }

        $isMultiline = false;
        $tokenAddedCount = 0;
        $indexWithNewLineNeeded = [];
        if ($shouldAddNewLine) {
            $indexWithNewLineNeeded += $this->shouldAddNewLineAfter($tokens, $begin);
        }

        for ($index = $begin + 1; $index < $end + $tokenAddedCount; ++$index) {
            $token = $tokens[$index];

            if (
                null !== $expressionDelimiters
                && \in_array($token->getContent(), $expressionDelimiters, true)
                && (null === $expressionDelimitersIndexes || \in_array($index, $expressionDelimitersIndexes, true))
            ) {
                if ($shouldAddNewLine) {
                    if ($enforceOneExpressionPerLine) {
                        $indexWithNewLineNeeded += $this->shouldAddNewLineAfter($tokens, $index);
                    } else {
                        $isMultiline = $isMultiline
                            || !array_values($this->shouldAddNewLineAfter($tokens, $index))[0]
                            || !array_values($this->shouldAddNewLineBefore($tokens, $index))[0];
                    }
                }

                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixArrays, true, [',']);

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            if (!$token->equals('(')) {
                continue;
            }

            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$prevIndex]->isGivenKind(T_ARRAY)) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixArrays, true, [',']);

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            if ($tokens[$prevIndex]->isGivenKind([T_IF, T_ELSEIF, T_WHILE])) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixControlStructures, false, ['&&', '||']);

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            if ($tokens[$prevIndex]->isGivenKind(T_FOREACH)) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixControlStructures, false, ['as']);

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            if ($tokens[$prevIndex]->isGivenKind(T_CATCH)) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixControlStructures, false, ['|']);

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            if ($tokens[$prevIndex]->isGivenKind(T_FOR)) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixControlStructures, true, [';']);

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            if (
                $tokens[$prevIndex]->equalsAny([']', [T_CLASS], [T_STRING], [T_VARIABLE], [T_STATIC]])
                && !$tokens[$prevPrevIndex]->isGivenKind(T_FUNCTION)
            ) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixArguments, true, [',']);

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            if (
                $tokens[$prevIndex]->isGivenKind([T_FN, T_FUNCTION])
                || $tokens[$prevIndex]->isGivenKind(T_STRING) && $tokens[$prevPrevIndex]->isGivenKind(T_FUNCTION)
            ) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixParameters, true, [',']);

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            if ($tokens[$prevIndex]->isGivenKind(T_SWITCH)) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $tokenAddedCount += $this->processBlock($tokens, $index, $until, $this->fixControlStructures, false, ['&&', '||']);

                $index = $tokens->getNextTokenOfKind($index, ['{']);
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                $added = $this->processBlock(
                    $tokens,
                    $index,
                    $until,
                    $this->fixSwitchCases,
                    true,
                    [':', ';'],
                    $this->getSwitchColonIndexes($tokens)
                );

                $index = $until + $added;
                $tokenAddedCount += $added;

                continue;
            }

            // @TODO: remove defined condition when PHP 8.0+ is required
            if (\defined('T_MATCH') && $tokens[$prevIndex]->isGivenKind(T_MATCH)) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $tokenAddedCount += $this->processBlock($tokens, $index, $until, $this->fixControlStructures, false, ['&&', '||']);

                $index = $tokens->getNextTokenOfKind($index, ['{']);
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                $added = $this->processBlock($tokens, $index, $until, $this->fixMatch, true, [',']);

                $index = $until + $added;
                $tokenAddedCount += $added;
            }
        }

        if ($shouldAddNewLine) {
            $indexWithNewLineNeeded += $this->shouldAddNewLineBefore($tokens, $end + $tokenAddedCount);
        }

        if ($isMultiline || \in_array(false, $indexWithNewLineNeeded, true)) {
            $increment = 0;
            foreach ($indexWithNewLineNeeded as $index => $shouldAddNewLineAfter) {
                if ($shouldAddNewLineAfter) {
                    $increment += $this->addNewLineAfter($tokens, $index + $increment);
                }
            }
            $tokenAddedCount += $increment;
        }

        return $tokenAddedCount;
    }

    /**
     * @return array<int>
     */
    private function getSwitchColonIndexes(Tokens $tokens): array
    {
        if (null === $this->switchColonIndexes) {
            $colonIndexes = [];

            /** @var SwitchAnalysis $analysis */
            foreach (ControlCaseStructuresAnalyzer::findControlStructures($tokens, [T_SWITCH]) as $analysis) {
                $default = $analysis->getDefaultAnalysis();

                if (null !== $default) {
                    $colonIndexes[] = $default->getColonIndex();
                }

                foreach ($analysis->getCases() as $caseAnalysis) {
                    $colonIndexes[] = $caseAnalysis->getColonIndex();
                }
            }

            $this->switchColonIndexes = $colonIndexes;
        }

        return $this->switchColonIndexes;
    }

    /**
     * @return non-empty-array<int, true>
     */
    private function shouldAddNewLineAfter(Tokens $tokens, int $index): array
    {
        $next = $tokens->getNextMeaningfulToken($index);
        if ($tokens->isPartialCodeMultiline($index, $next - 1)) {
            return [$index => false];
        }

        return [$index => true];
    }

    /**
     * @return non-empty-array<int, true>
     */
    private function shouldAddNewLineBefore(Tokens $tokens, int $index): array
    {
        $previous = $tokens->getPrevMeaningfulToken($index);
        if ($tokens->isPartialCodeMultiline($previous, $index)) {
            return [$previous => false];
        }

        return [$previous => true];
    }

    private function addNewLineAfter(Tokens $tokens, int $index): int
    {
        if ($tokens[$index + 1]->isWhitespace()) {
            $tokens[$index + 1] = new Token([T_WHITESPACE, "\n"]);

            return 0;
        }

        $tokens->insertSlices([$index + 1 => new Token([T_WHITESPACE, "\n"])]);

        return 1;
    }
}
