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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\AlternativeSyntaxAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\ControlCaseStructuresAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\GotoLabelAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\ReferenceAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class OperatorLinebreakFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const BOOLEAN_OPERATORS = [[T_BOOLEAN_AND], [T_BOOLEAN_OR], [T_LOGICAL_AND], [T_LOGICAL_OR], [T_LOGICAL_XOR]];

    private string $position = 'beginning';

    /**
     * @var array<array<int|string>|string>
     */
    private array $operators = [];

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Operators - when multiline - must always be at the beginning or at the end of the line.',
            [
                new CodeSample('<?php
function foo() {
    return $bar ||
        $baz;
}
'),
                new CodeSample(
                    '<?php
function foo() {
    return $bar
        || $baz;
}
',
                    ['position' => 'end']
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->position = $this->configuration['position'];
        $this->operators = self::BOOLEAN_OPERATORS;

        if (false === $this->configuration['only_booleans']) {
            $this->operators = array_merge($this->operators, self::getNonBooleanOperators());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('only_booleans', 'whether to limit operators to only boolean ones'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('position', 'whether to place operators at the beginning or at the end of the line'))
                ->setAllowedValues(['beginning', 'end'])
                ->setDefault($this->position)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $referenceAnalyzer = new ReferenceAnalyzer();
        $gotoLabelAnalyzer = new GotoLabelAnalyzer();
        $alternativeSyntaxAnalyzer = new AlternativeSyntaxAnalyzer();

        $excludedIndices = $this->getExcludedIndices($tokens);

        $index = $tokens->count();
        while ($index > 1) {
            --$index;

            if (!$tokens[$index]->equalsAny($this->operators, false)) {
                continue;
            }

            if ($gotoLabelAnalyzer->belongsToGoToLabel($tokens, $index)) {
                continue;
            }

            if ($referenceAnalyzer->isReference($tokens, $index)) {
                continue;
            }

            if ($alternativeSyntaxAnalyzer->belongsToAlternativeSyntax($tokens, $index)) {
                continue;
            }

            if (\in_array($index, $excludedIndices, true)) {
                continue;
            }

            $operatorIndices = [$index];
            if ($tokens[$index]->equals(':')) {
                /** @var int $prevIndex */
                $prevIndex = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$prevIndex]->equals('?')) {
                    $operatorIndices = [$prevIndex, $index];
                    $index = $prevIndex;
                }
            }

            $this->fixOperatorLinebreak($tokens, $operatorIndices);
        }
    }

    /**
     * Currently only colons from "switch".
     *
     * @return int[]
     */
    private function getExcludedIndices(Tokens $tokens): array
    {
        $colonIndices = [];

        foreach (ControlCaseStructuresAnalyzer::findControlStructures($tokens, [T_SWITCH]) as $analysis) {
            foreach ($analysis->getCases() as $case) {
                $colonIndices[] = $case->getColonIndex();
            }

            if ($analysis instanceof SwitchAnalysis) {
                $defaultAnalysis = $analysis->getDefaultAnalysis();

                if (null !== $defaultAnalysis) {
                    $colonIndices[] = $defaultAnalysis->getColonIndex();
                }
            }
        }

        return $colonIndices;
    }

    /**
     * @param int[] $operatorIndices
     */
    private function fixOperatorLinebreak(Tokens $tokens, array $operatorIndices): void
    {
        /** @var int $prevIndex */
        $prevIndex = $tokens->getPrevMeaningfulToken(min($operatorIndices));
        $indexStart = $prevIndex + 1;

        /** @var int $nextIndex */
        $nextIndex = $tokens->getNextMeaningfulToken(max($operatorIndices));
        $indexEnd = $nextIndex - 1;

        if (!$this->isMultiline($tokens, $indexStart, $indexEnd)) {
            return; // operator is not surrounded by multiline whitespaces, do not touch it
        }

        if ('beginning' === $this->position) {
            if (!$this->isMultiline($tokens, max($operatorIndices), $indexEnd)) {
                return; // operator already is placed correctly
            }
            $this->fixMoveToTheBeginning($tokens, $operatorIndices);

            return;
        }

        if (!$this->isMultiline($tokens, $indexStart, min($operatorIndices))) {
            return; // operator already is placed correctly
        }
        $this->fixMoveToTheEnd($tokens, $operatorIndices);
    }

    /**
     * @param int[] $operatorIndices
     */
    private function fixMoveToTheBeginning(Tokens $tokens, array $operatorIndices): void
    {
        /** @var int $prevIndex */
        $prevIndex = $tokens->getNonEmptySibling(min($operatorIndices), -1);

        /** @var int $nextIndex */
        $nextIndex = $tokens->getNextMeaningfulToken(max($operatorIndices));

        for ($i = $nextIndex - 1; $i > max($operatorIndices); --$i) {
            if ($tokens[$i]->isWhitespace() && 1 === Preg::match('/\R/u', $tokens[$i]->getContent())) {
                $isWhitespaceBefore = $tokens[$prevIndex]->isWhitespace();
                $inserts = $this->getReplacementsAndClear($tokens, $operatorIndices, -1);
                if ($isWhitespaceBefore) {
                    $inserts[] = new Token([T_WHITESPACE, ' ']);
                }
                $tokens->insertAt($nextIndex, $inserts);

                break;
            }
        }
    }

    /**
     * @param int[] $operatorIndices
     */
    private function fixMoveToTheEnd(Tokens $tokens, array $operatorIndices): void
    {
        /** @var int $prevIndex */
        $prevIndex = $tokens->getPrevMeaningfulToken(min($operatorIndices));

        /** @var int $nextIndex */
        $nextIndex = $tokens->getNonEmptySibling(max($operatorIndices), 1);

        for ($i = $prevIndex + 1; $i < max($operatorIndices); ++$i) {
            if ($tokens[$i]->isWhitespace() && 1 === Preg::match('/\R/u', $tokens[$i]->getContent())) {
                $isWhitespaceAfter = $tokens[$nextIndex]->isWhitespace();
                $inserts = $this->getReplacementsAndClear($tokens, $operatorIndices, 1);
                if ($isWhitespaceAfter) {
                    array_unshift($inserts, new Token([T_WHITESPACE, ' ']));
                }
                $tokens->insertAt($prevIndex + 1, $inserts);

                break;
            }
        }
    }

    /**
     * @param int[] $indices
     *
     * @return Token[]
     */
    private function getReplacementsAndClear(Tokens $tokens, array $indices, int $direction): array
    {
        return array_map(
            static function (int $index) use ($tokens, $direction): Token {
                $clone = $tokens[$index];

                if ($tokens[$index + $direction]->isWhitespace()) {
                    $tokens->clearAt($index + $direction);
                }

                $tokens->clearAt($index);

                return $clone;
            },
            $indices
        );
    }

    private function isMultiline(Tokens $tokens, int $indexStart, int $indexEnd): bool
    {
        for ($index = $indexStart; $index <= $indexEnd; ++$index) {
            if (str_contains($tokens[$index]->getContent(), "\n")) {
                return true;
            }
        }

        return false;
    }

    private static function getNonBooleanOperators(): array
    {
        return array_merge(
            [
                '%', '&', '*', '+', '-', '.', '/', ':', '<', '=', '>', '?', '^', '|',
                [T_AND_EQUAL], [T_CONCAT_EQUAL], [T_DIV_EQUAL], [T_DOUBLE_ARROW], [T_IS_EQUAL], [T_IS_GREATER_OR_EQUAL],
                [T_IS_IDENTICAL], [T_IS_NOT_EQUAL], [T_IS_NOT_IDENTICAL], [T_IS_SMALLER_OR_EQUAL], [T_MINUS_EQUAL],
                [T_MOD_EQUAL], [T_MUL_EQUAL], [T_OR_EQUAL], [T_PAAMAYIM_NEKUDOTAYIM], [T_PLUS_EQUAL], [T_POW],
                [T_POW_EQUAL], [T_SL], [T_SL_EQUAL], [T_SR], [T_SR_EQUAL], [T_XOR_EQUAL],
                [T_COALESCE], [T_SPACESHIP],
            ],
            array_map(static function ($id): array { return [$id]; }, Token::getObjectOperatorKinds())
        );
    }
}
