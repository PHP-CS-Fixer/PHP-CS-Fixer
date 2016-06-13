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

namespace Symfony\CS;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
abstract class AbstractConsecutiveBuiltInFunctionsFixer extends AbstractFixer
{
    /**
     * @param Tokens $tokens
     * @param int    $givenKind
     */
    final protected function combineBuiltInFunctions(Tokens $tokens, $givenKind, $givenSequence)
    {
        foreach ($tokens->findGivenKind($givenKind) as $index => $token) {
            $this->findAndReplaceTrailingBuiltInFunctions($tokens, $index, $givenSequence);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $firstIssetIndex
     */
    final private function findAndReplaceTrailingBuiltInFunctions(Tokens $tokens, $firstIssetIndex, $givenSequence)
    {
        $openParenthesisIndex = $tokens->getNextMeaningfulToken($firstIssetIndex);
        if (!$tokens[$openParenthesisIndex]->equals('(')) {
            return;
        }
        $closeParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesisIndex);

        $neededSequence = $tokens->findSequence($givenSequence, $closeParenthesisIndex);
        if ($neededSequence) {
            $neededSequenceIndexes = array_keys($neededSequence);
            $neededSequenceBeginIndex = current($neededSequenceIndexes);
            $neededSequenceEndIndex = end($neededSequenceIndexes);

            // Have to clone the token to don't lose them after the range clear.
            $commentTokens = array_map(function (Token $token) {
                return clone $token;
            }, $tokens->findGivenKind(T_COMMENT, $neededSequenceBeginIndex, $neededSequenceEndIndex));

            // Remove sequence to merge the function arguments
            $tokens->clearRange($neededSequenceBeginIndex, $neededSequenceEndIndex);
            $tokens[$neededSequenceEndIndex]->setContent(',');
            $tokens->insertAt($neededSequenceEndIndex + 1, new Token(array(T_WHITESPACE, ' ')));

            // Insert comments at the end of the merged function
            $tokens->insertAt($tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesisIndex) + 2, array_values($commentTokens));

            // Call again the method if more trailing built in function.
            $this->findAndReplaceTrailingBuiltInFunctions($tokens, $firstIssetIndex, $givenSequence);
        }
    }
}
