<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\TokensAnalyzer;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
class SelfAccessorFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($i = 0, $c = $tokens->count(); $i < $c; ++$i) {
            if (!$tokens[$i]->isClassy()) {
                continue;
            }

            $nameIndex = $tokens->getNextTokenOfKind($i, array(array(T_STRING)));
            $startIndex = $tokens->getNextTokenOfKind($nameIndex, array('{'));
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);

            $name = $tokens[$nameIndex]->getContent();

            $this->replaceNameOccurrences($tokens, $name, $startIndex, $endIndex);

            // continue after the class declaration
            $i = $endIndex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Inside a classy element "self" should be preferred to the class name itself.';
    }

    /**
     * Replace occurrences of the name of the classy element by "self" (if possible).
     *
     * @param Tokens $tokens
     * @param string $name
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function replaceNameOccurrences(Tokens $tokens, $name, $startIndex, $endIndex)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($i = $startIndex; $i < $endIndex; ++$i) {
            $token = $tokens[$i];

            // skip lambda functions (PHP < 5.4 compatibility)
            if ($token->isGivenKind(T_FUNCTION) && $tokensAnalyzer->isLambda($i)) {
                $i = $tokens->getNextTokenOfKind($i, array('{'));
                $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $i);
                continue;
            }

            if (!$token->equals(array(T_STRING, $name), false)) {
                continue;
            }

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($i)];
            $nextToken = $tokens[$tokens->getNextMeaningfulToken($i)];

            // skip tokens that are part of a fully qualified name
            if ($prevToken->isGivenKind(T_NS_SEPARATOR) || $nextToken->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            if (
                $prevToken->isGivenKind(array(T_INSTANCEOF, T_NEW)) ||
                $nextToken->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)
            ) {
                $token->setContent('self');
            }
        }
    }
}
