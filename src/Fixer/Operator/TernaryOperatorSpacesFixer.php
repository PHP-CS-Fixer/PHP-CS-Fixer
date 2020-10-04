<?php

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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\SwitchAnalyzer;
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'Standardize spaces around ternary operator.',
            [new CodeSample("<?php \$a = \$a   ?1 :0;\n")]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after ArraySyntaxFixer, ListSyntaxFixer.
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(['?', ':']);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $ternaryOperatorIndices = [];
        $excludedIndices = [];

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_SWITCH)) {
                $excludedIndices = array_merge($excludedIndices, $this->getColonIndicesForSwitch($tokens, $index));

                continue;
            }

            if (!$token->equalsAny(['?', ':'])) {
                continue;
            }

            if (\in_array($index, $excludedIndices, true)) {
                continue;
            }

            if ($this->belongsToGoToLabel($tokens, $index)) {
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

    /**
     * @param int $index
     *
     * @return bool
     */
    private function belongsToGoToLabel(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->equals(':')) {
            return false;
        }

        $prevMeaningfulTokenIndex = $tokens->getPrevMeaningfulToken($index);

        if (!$tokens[$prevMeaningfulTokenIndex]->isGivenKind(T_STRING)) {
            return false;
        }

        $prevMeaningfulTokenIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulTokenIndex);

        return $tokens[$prevMeaningfulTokenIndex]->equalsAny([';', '{', '}', [T_OPEN_TAG]]);
    }

    /**
     * @param int $switchIndex
     *
     * @return int[]
     */
    private function getColonIndicesForSwitch(Tokens $tokens, $switchIndex)
    {
        return array_map(
            static function (CaseAnalysis $caseAnalysis) {
                return $caseAnalysis->getColonIndex();
            },
            (new SwitchAnalyzer())->getSwitchAnalysis($tokens, $switchIndex)->getCases()
        );
    }

    /**
     * @param int  $index
     * @param bool $after
     */
    private function ensureWhitespaceExistence(Tokens $tokens, $index, $after)
    {
        if ($tokens[$index]->isWhitespace()) {
            if (
                false === strpos($tokens[$index]->getContent(), "\n")
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
