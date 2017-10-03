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

use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
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
abstract class AbstractLinesBeforeNamespaceFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
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
        $precedingNewlines = 0;
        $newlineInOpening = false;
        $openingToken = null;
        for ($i = 1; $i <= 2; ++$i) {
            if (isset($tokens[$index - $i])) {
                $token = $tokens[$index - $i];
                if ($token->isGivenKind(T_OPEN_TAG)) {
                    $openingToken = $token;
                    $openingTokenIndex = $index - $i;
                    $newlineInOpening = strpos($token->getContent(), "\n") !== false;
                    if ($newlineInOpening) {
                        ++$precedingNewlines;
                    }
                    break;
                }
                if (false === $token->isGivenKind(T_WHITESPACE)) {
                    break;
                }
                $precedingNewlines += substr_count($token->getContent(), "\n");
            }
        }

        if ($expected === $precedingNewlines) {
            return;
        }

        $previousIndex = $index - 1;
        $previous = $tokens[$previousIndex];

        if (0 === $expected) {
            // Remove all the previous new lines
            if ($previous->isWhitespace()) {
                $tokens->clearAt($previousIndex);
            }
            // Remove new lines in opening token
            if ($newlineInOpening) {
                $tokens[$openingTokenIndex] = new Token(array(T_OPEN_TAG, rtrim($openingToken->getContent()).' '));
            }

            return;
        }

        $lineEnding = $this->whitespacesConfig->getLineEnding();
        $newlinesForWhitespaceToken = $expected;
        if (null !== $openingToken) {
            // Use the configured line ending for the PHP opening tag
            $content = rtrim($openingToken->getContent());
            $newContent = $content.$lineEnding;
            $tokens[$openingTokenIndex] = new Token(array(T_OPEN_TAG, $newContent));
            --$newlinesForWhitespaceToken;
        }
        if ($previous->isWhitespace()) {
            if (0 === $newlinesForWhitespaceToken) {
                // We have all the needed new lines in the opening tag
                // Let's remove the previous token containing extra new lines
                $tokens->clearAt($previousIndex);
            } else {
                // Fix the previous whitespace token
                $tokens[$previousIndex] = new Token(array(T_WHITESPACE, str_repeat($lineEnding, $newlinesForWhitespaceToken)));
            }
        } elseif (0 < $newlinesForWhitespaceToken) {
            // Add a new whitespace token
            $tokens->insertAt($index, new Token(array(T_WHITESPACE, str_repeat($lineEnding, $newlinesForWhitespaceToken))));
        }
    }
}
