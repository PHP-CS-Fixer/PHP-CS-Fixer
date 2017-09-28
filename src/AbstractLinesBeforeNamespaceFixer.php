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

namespace PhpCsFixer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * This abstract fixer is responsible for ensuring that a certain number of
 * lines prefix a namespace declaration.
 *
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 */
abstract class AbstractLinesBeforeNamespaceFixer extends AbstractFixer
{
    /**
     * Make sure the expected number of new lines prefix a namespace.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $expected
     */
    protected function fixLinesBeforeNamespace(Tokens $tokens, $index, $expected)
    {
        // Let's determine the total numbers of new lines before the namespace
        // and the opening token
        $precedingNewlinesTotal = 0;
        $precedingNewlinesInOpening = 0;
        $openingToken = null;
        for ($i = 1; $i <= 2; ++$i) {
            if (isset($tokens[$index - $i])) {
                $token = $tokens[$index - $i];
                if ($token->isGivenKind(T_OPEN_TAG)) {
                    $openingToken = $token;
                    $precedingNewlinesInOpening = substr_count($token->getContent(), "\n");
                    $precedingNewlinesTotal += $precedingNewlinesInOpening;
                    break;
                }
                if (false === $token->isGivenKind(T_WHITESPACE)) {
                    break;
                }
                $precedingNewlinesTotal += substr_count($token->getContent(), "\n");
            }
        }

        if ($expected !== $precedingNewlinesTotal) {
            $previousIndex = $index - 1;
            $previous = $tokens[$previousIndex];
            if (0 === $expected) {
                // Remove all the previous new lines
                if ($previous->isWhitespace()) {
                    $tokens->clearAt($previousIndex);
                }
                // Remove new lines in opening token
                if (0 < $precedingNewlinesInOpening) {
                    $openingToken->setContent(rtrim($openingToken->getContent()).' ');
                }
            } else {
                if (null !== $openingToken && 0 === $precedingNewlinesInOpening) {
                    // We have an opening tag without new lines: add a new line there
                    $openingToken->setContent(rtrim($openingToken->getContent())."\n");
                    ++$precedingNewlinesInOpening;
                }
                $newlinesForWhitespaceToken = $expected - $precedingNewlinesInOpening;
                if ($previous->isWhitespace()) {
                    if (0 === $newlinesForWhitespaceToken) {
                        // We have all the needed new lines in the opening tag
                        // Let's remove the previous token containing extra new lines
                        $tokens->clearAt($previousIndex);
                    } else {
                        // Fix the previous whitespace token
                        $previous->setContent(str_repeat("\n", $newlinesForWhitespaceToken));
                    }
                } elseif (0 < $newlinesForWhitespaceToken) {
                    // Add a new whitespace token
                    $tokens->insertAt($index, new Token(array(T_WHITESPACE, str_repeat("\n", $newlinesForWhitespaceToken))));
                }
            }
        }
    }
}
