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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class PostIncrementFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Post incrementation/decrementation should be used if possible.',
            [new CodeSample("<?php\n++\$a;\n--\$b;")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_INC, T_DEC]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind([T_INC, T_DEC]) || !$tokensAnalyzer->isUnaryPredecessorOperator($index)) {
                continue;
            }

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
            if (!$prevToken->equalsAny([';', '{', '}', [T_OPEN_TAG]])) {
                continue;
            }

            $endIndex = $this->findEnd($tokens, $index);

            $nextToken = $tokens[$tokens->getNextMeaningfulToken($endIndex)];
            if ($nextToken->equalsAny([';', ')'])) {
                $tokens->clearAt($index);
                $tokens->insertAt($tokens->getNextNonWhitespace($endIndex), clone $token);
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function findEnd(Tokens $tokens, $index)
    {
        $nextIndex = $tokens->getNextMeaningfulToken($index);
        $nextToken = $tokens[$nextIndex];

        while ($nextToken->equalsAny([
            '$',
            '[',
            [CT::T_DYNAMIC_PROP_BRACE_OPEN],
            [CT::T_DYNAMIC_VAR_BRACE_OPEN],
            [T_NS_SEPARATOR],
            [T_STRING],
            [T_VARIABLE],
        ])) {
            $index = $nextIndex;

            $blockType = Tokens::detectBlockType($nextToken);
            if (null !== $blockType && $blockType['isStart']) {
                $nextIndex = $tokens->findBlockEnd($blockType['type'], $nextIndex);
                $index = $nextIndex;
            }
            $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
            $nextToken = $tokens[$nextIndex];
        }

        if ($nextToken->isGivenKind(T_OBJECT_OPERATOR)) {
            return $this->findEnd($tokens, $nextIndex);
        }

        if ($nextToken->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)) {
            $nextNextToken = $tokens->getNextMeaningfulToken($nextIndex);
            if (!$tokens[$nextNextToken]->isGivenKind(T_STRING)) {
                return $this->findEnd($tokens, $nextNextToken);
            }

            $index = $tokens->getTokenNotOfKindSibling($nextIndex, 1, [[T_NS_SEPARATOR], [T_STRING]]);
            $index = $tokens->getPrevMeaningfulToken($index);
        }

        return $index;
    }
}
