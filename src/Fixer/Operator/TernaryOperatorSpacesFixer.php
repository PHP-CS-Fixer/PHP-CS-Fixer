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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\ControlCaseStructuresAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\GotoLabelAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class TernaryOperatorSpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Standardize spaces around ternary operator.',
            [new CodeSample("<?php \$a = \$a   ?1 :0;\n")]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after ArraySyntaxFixer, ListSyntaxFixer, TernaryToElvisOperatorFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound(['?', ':']);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $gotoLabelAnalyzer = new GotoLabelAnalyzer();
        $ternaryOperatorIndices = [];
        $excludedIndices = $this->getColonIndicesForSwitch($tokens);

        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny(['?', ':'])) {
                continue;
            }

            if (\in_array($index, $excludedIndices, true)) {
                continue;
            }

            if ($this->belongsToAlternativeSyntax($tokens, $index)) {
                continue;
            }

            if ($gotoLabelAnalyzer->belongsToGoToLabel($tokens, $index)) {
                continue;
            }

            $ternaryOperatorIndices[] = $index;
        }

        foreach (array_reverse($ternaryOperatorIndices) as $index) {
            $token = $tokens[$index];

            if ($token->equals('?')) {
                $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($index);

                if ($tokens[$nextNonWhitespaceIndex]->equals(':')) {
                    // for `$a ?: $b` remove spaces between `?` and `:`
                    $tokens->ensureWhitespaceAtIndex($index + 1, 0, '');
                } else {
                    // for `$a ? $b : $c` ensure space after `?`
                    $this->ensureWhitespaceExistence($tokens, $index + 1, true);
                }

                // for `$a ? $b : $c` ensure space before `?`
                $this->ensureWhitespaceExistence($tokens, $index - 1, false);

                continue;
            }

            if ($token->equals(':')) {
                // for `$a ? $b : $c` ensure space after `:`
                $this->ensureWhitespaceExistence($tokens, $index + 1, true);

                $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

                if (!$prevNonWhitespaceToken->equals('?')) {
                    // for `$a ? $b : $c` ensure space before `:`
                    $this->ensureWhitespaceExistence($tokens, $index - 1, false);
                }
            }
        }
    }

    private function belongsToAlternativeSyntax(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->equals(':')) {
            return false;
        }

        $closeParenthesisIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$closeParenthesisIndex]->isGivenKind(T_ELSE)) {
            return true;
        }
        if (!$tokens[$closeParenthesisIndex]->equals(')')) {
            return false;
        }

        $openParenthesisIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $closeParenthesisIndex);

        $alternativeControlStructureIndex = $tokens->getPrevMeaningfulToken($openParenthesisIndex);

        return $tokens[$alternativeControlStructureIndex]->isGivenKind([T_DECLARE, T_ELSEIF, T_FOR, T_FOREACH, T_IF, T_SWITCH, T_WHILE]);
    }

    /**
     * @return int[]
     */
    private function getColonIndicesForSwitch(Tokens $tokens): array
    {
        $colonIndexes = [];

        foreach (ControlCaseStructuresAnalyzer::findControlStructures($tokens, [T_SWITCH]) as $analysis) {
            foreach ($analysis->getCases() as $case) {
                $colonIndexes[] = $case->getColonIndex();
            }

            if ($analysis instanceof SwitchAnalysis) {
                $defaultAnalysis = $analysis->getDefaultAnalysis();

                if (null !== $defaultAnalysis) {
                    $colonIndexes[] = $defaultAnalysis->getColonIndex();
                }
            }
        }

        return $colonIndexes;
    }

    private function ensureWhitespaceExistence(Tokens $tokens, int $index, bool $after): void
    {
        if ($tokens[$index]->isWhitespace()) {
            if (
                !str_contains($tokens[$index]->getContent(), "\n")
                && !$tokens[$index - 1]->isComment()
            ) {
                $tokens[$index] = new Token([T_WHITESPACE, ' ']);
            }

            return;
        }

        $index += $after ? 0 : 1;
        $tokens->insertAt($index, new Token([T_WHITESPACE, ' ']));
    }
}
