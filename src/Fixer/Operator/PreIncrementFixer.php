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
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class PreIncrementFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Pre incrementation/decrementation should be used if possible.',
            array(new CodeSample("<?php\n\$a++;\n\$b--;"))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_INC, T_DEC));
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(array(T_INC, T_DEC)) || !$tokensAnalyzer->isUnarySuccessorOperator($index)) {
                continue;
            }

            $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];
            if (!$nextToken->equalsAny(array(';', ')'))) {
                continue;
            }

            $startIndex = $this->findStart($tokens, $index);

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($startIndex)];
            if ($prevToken->equalsAny(array(';', '{', '}', array(T_OPEN_TAG)))) {
                $tokens->clearAt($index);
                $tokens->insertAt($startIndex, clone $token);
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function findStart(Tokens $tokens, $index)
    {
        do {
            $index = $tokens->getPrevMeaningfulToken($index);
            $token = $tokens[$index];

            $blockType = Tokens::detectBlockType($token);
            if (null !== $blockType && !$blockType['isStart']) {
                $index = $tokens->findBlockStart($blockType['type'], $index);
                $token = $tokens[$index];
            }
        } while (!$token->equalsAny(array('$', array(T_VARIABLE))));

        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        $prevToken = $tokens[$prevIndex];

        if ($prevToken->equals('$')) {
            $index = $prevIndex;
            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevIndex];
        }

        if ($prevToken->isGivenKind(T_OBJECT_OPERATOR)) {
            return $this->findStart($tokens, $prevIndex);
        }

        if ($prevToken->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)) {
            $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            if (!$tokens[$prevPrevIndex]->isGivenKind(T_STRING)) {
                return $this->findStart($tokens, $prevIndex);
            }

            $index = $tokens->getTokenNotOfKindSibling($prevIndex, -1, array(array(T_NS_SEPARATOR), array(T_STRING)));
            $index = $tokens->getNextMeaningfulToken($index);
        }

        return $index;
    }
}
