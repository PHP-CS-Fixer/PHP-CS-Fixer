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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class SwitchAnalyzer
{
    /**
     * @param int $switchIndex
     *
     * @return SwitchAnalysis
     */
    public function getSwitchAnalysis(Tokens $tokens, $switchIndex)
    {
        if (!$tokens[$switchIndex]->isGivenKind(T_SWITCH)) {
            throw new \InvalidArgumentException(sprintf('Index %d is not "switch".', $switchIndex));
        }

        $casesStartIndex = $this->getCasesStart($tokens, $switchIndex);
        $casesEndIndex = $this->getCasesEnd($tokens, $casesStartIndex);

        $cases = [];
        $ternaryOperatorDepth = 0;
        $index = $casesStartIndex;
        while ($index < $casesEndIndex) {
            ++$index;
            if ($tokens[$index]->isGivenKind(T_SWITCH)) {
                $index = (new self())->getSwitchAnalysis($tokens, $index)->getCasesEnd();

                continue;
            }
            if ($tokens[$index]->equals('?')) {
                ++$ternaryOperatorDepth;

                continue;
            }
            if (!$tokens[$index]->equals(':')) {
                continue;
            }
            if ($ternaryOperatorDepth > 0) {
                --$ternaryOperatorDepth;

                continue;
            }
            $cases[] = new CaseAnalysis($index);
        }

        return new SwitchAnalysis($casesStartIndex, $casesEndIndex, $cases);
    }

    /**
     * @param int $switchIndex
     *
     * @return int
     */
    private function getCasesStart(Tokens $tokens, $switchIndex)
    {
        /** @var int $parenthesisStartIndex */
        $parenthesisStartIndex = $tokens->getNextMeaningfulToken($switchIndex);
        $parenthesisEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $parenthesisStartIndex);

        $casesStartIndex = $tokens->getNextMeaningfulToken($parenthesisEndIndex);
        \assert(\is_int($casesStartIndex));

        return $casesStartIndex;
    }

    /**
     * @param int $casesStartIndex
     *
     * @return int
     */
    private function getCasesEnd(Tokens $tokens, $casesStartIndex)
    {
        if ($tokens[$casesStartIndex]->equals('{')) {
            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $casesStartIndex);
        }

        $depth = 1;
        $index = $casesStartIndex;
        while ($depth > 0) {
            /** @var int $index */
            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->isGivenKind(T_ENDSWITCH)) {
                --$depth;

                continue;
            }

            if (!$tokens[$index]->isGivenKind(T_SWITCH)) {
                continue;
            }
            $index = $this->getCasesStart($tokens, $index);
            if ($tokens[$index]->equals(':')) {
                ++$depth;
            }
        }

        /** @var int $afterEndswitchIndex */
        $afterEndswitchIndex = $tokens->getNextMeaningfulToken($index);

        return $tokens[$afterEndswitchIndex]->equals(';') ? $afterEndswitchIndex : $index;
    }
}
