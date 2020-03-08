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
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class AssignNullCoalescingToCoalesceEqualFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Use the null coalescing assignment operator `??=` where possible.',
            [
                new VersionSpecificCodeSample(
                    "<?php\n\$foo = \$foo ?? 1;\n",
                    new VersionSpecification(70400)
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer, NoWhitespaceInBlankLineFixer.
     * Must run after TernaryToNullCoalescingFixer.
     */
    public function getPriority(): int
    {
        return -1;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return \defined('T_COALESCE_EQUAL') && $tokens->isTokenKindFound(T_COALESCE);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = \count($tokens) - 1; $index > 3; --$index) {
            if (!$tokens[$index]->isGivenKind(T_COALESCE)) {
                continue;
            }

            // make sure after '??' does not contain '? :'

            $nextIndex = $tokens->getNextTokenOfKind($index, ['?', ';', [T_CLOSE_TAG]]);

            if ($tokens[$nextIndex]->equals('?')) {
                continue;
            }

            // get what is before '??'

            $beforeRange = $this->getBeforeOperator($tokens, $index);
            $equalsIndex = $tokens->getPrevMeaningfulToken($beforeRange['start']);

            // make sure that before that is '='

            if (!$tokens[$equalsIndex]->equals('=')) {
                continue;
            }

            // get what is before '='

            $assignRange = $this->getBeforeOperator($tokens, $equalsIndex);
            $beforeAssignmentIndex = $tokens->getPrevMeaningfulToken($assignRange['start']);

            // make sure that before that is ';', '{', '}', '(', ')' or '<php'

            if (!$tokens[$beforeAssignmentIndex]->equalsAny([';', '{', '}', ')', '(', [T_OPEN_TAG]])) {
                continue;
            }

            // make sure before and after are the same

            if (!$this->rangeEqualsRange($tokens, $assignRange, $beforeRange)) {
                continue;
            }

            $tokens[$equalsIndex] = new Token([T_COALESCE_EQUAL, '??=']);
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            $this->clearMeaningfulFromRange($tokens, $beforeRange);

            foreach ([$equalsIndex, $assignRange['end']] as $i) {
                $i = $tokens->getNonEmptySibling($i, 1);

                if ($tokens[$i]->isWhitespace(" \t")) {
                    $tokens[$i] = new Token([T_WHITESPACE, ' ']);
                } elseif (!$tokens[$i]->isWhitespace()) {
                    $tokens->insertAt($i, new Token([T_WHITESPACE, ' ']));
                }
            }
        }
    }

    private function getBeforeOperator(Tokens $tokens, int $index): array
    {
        $controlStructureWithoutBracesTypes = [T_IF, T_ELSE, T_ELSEIF, T_FOR, T_FOREACH, T_WHILE];

        $index = $tokens->getPrevMeaningfulToken($index);
        $range = [
            'start' => $index,
            'end' => $index,
        ];

        $previousIndex = $index;
        $previousToken = $tokens[$previousIndex];

        while ($previousToken->equalsAny([
            '$',
            ']',
            ')',
            [CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE],
            [CT::T_DYNAMIC_PROP_BRACE_CLOSE],
            [CT::T_DYNAMIC_VAR_BRACE_CLOSE],
            [T_NS_SEPARATOR],
            [T_STRING],
            [T_VARIABLE],
        ])) {
            $blockType = Tokens::detectBlockType($previousToken);

            if (null !== $blockType) {
                $blockStart = $tokens->findBlockStart($blockType['type'], $previousIndex);

                if ($tokens[$previousIndex]->equals(')') && $tokens[$tokens->getPrevMeaningfulToken($blockStart)]->isGivenKind($controlStructureWithoutBracesTypes)) {
                    break; // we went too far back
                }

                $previousIndex = $blockStart;
            }

            $index = $previousIndex;
            $previousIndex = $tokens->getPrevMeaningfulToken($previousIndex);
            $previousToken = $tokens[$previousIndex];
        }

        if ($previousToken->isGivenKind(T_OBJECT_OPERATOR)) {
            $index = $this->getBeforeOperator($tokens, $previousIndex)['start'];
        } elseif ($previousToken->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)) {
            $index = $this->getBeforeOperator($tokens, $tokens->getPrevMeaningfulToken($previousIndex))['start'];
        }

        $range['start'] = $index;

        return $range;
    }

    /**
     * Meaningful compare of tokens within ranges.
     */
    private function rangeEqualsRange(Tokens $tokens, array $range1, array $range2): bool
    {
        $leftStart = $range1['start'];
        $leftEnd = $range1['end'];

        while ($tokens[$leftStart]->equals('(') && $tokens[$leftEnd]->equals(')')) {
            $leftStart = $tokens->getNextMeaningfulToken($leftStart);
            $leftEnd = $tokens->getPrevMeaningfulToken($leftEnd);
        }

        $rightStart = $range2['start'];
        $rightEnd = $range2['end'];

        while ($tokens[$rightStart]->equals('(') && $tokens[$rightEnd]->equals(')')) {
            $rightStart = $tokens->getNextMeaningfulToken($rightStart);
            $rightEnd = $tokens->getPrevMeaningfulToken($rightEnd);
        }

        while ($leftStart <= $leftEnd && $rightStart <= $rightEnd) {
            if (
                !$tokens[$leftStart]->equals($tokens[$rightStart])
                && !($tokens[$leftStart]->equalsAny(['[', [CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN]]) && $tokens[$rightStart]->equalsAny(['[', [CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN]]))
                && !($tokens[$leftStart]->equalsAny([']', [CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE]]) && $tokens[$rightStart]->equalsAny([']', [CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE]]))
            ) {
                return false;
            }

            $leftStart = $tokens->getNextMeaningfulToken($leftStart);
            $rightStart = $tokens->getNextMeaningfulToken($rightStart);
        }

        return $leftStart > $leftEnd && $rightStart > $rightEnd;
    }

    private function clearMeaningfulFromRange(Tokens $tokens, array $range): void
    {
        // $range['end'] must be meaningful!
        for ($i = $range['end']; $i >= $range['start']; $i = $tokens->getPrevMeaningfulToken($i)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($i);
        }
    }
}
