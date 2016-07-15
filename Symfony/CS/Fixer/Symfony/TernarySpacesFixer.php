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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TernarySpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $ternaryLevel = 0;
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if ($token->isArray()) {
                continue;
            }

            if ($token->equals('?')) {
                ++$ternaryLevel;

                $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($index);
                $nextNonWhitespaceToken = $tokens[$nextNonWhitespaceIndex];

                if ($nextNonWhitespaceToken->equals(':')) {
                    // for `$a ?: $b` remove spaces between `?` and `:`
                    if ($tokens[$index + 1]->isWhitespace()) {
                        $tokens[$index + 1]->clear();
                    }
                } else {
                    // for `$a ? $b : $c` ensure space after `?`
                    $this->ensureWhitespaceExistence($tokens, $index + 1, true);
                }

                // for `$a ? $b : $c` ensure space before `?`
                $this->ensureWhitespaceExistence($tokens, $index - 1, false);

                continue;
            }

            if ($ternaryLevel && $token->equals(':')) {
                // for `$a ? $b : $c` ensure space after `:`
                $this->ensureWhitespaceExistence($tokens, $index + 1, true);

                $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

                if (!$prevNonWhitespaceToken->equals('?')) {
                    // for `$a ? $b : $c` ensure space before `:`
                    $this->ensureWhitespaceExistence($tokens, $index - 1, false);
                }

                --$ternaryLevel;
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Standardize spaces around ternary operator.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param bool   $after
     */
    private function ensureWhitespaceExistence(Tokens $tokens, $index, $after)
    {
        if ($tokens[$index]->isWhitespace()) {
            if (false === strpos($tokens[$index]->getContent(), "\n")) {
                // comment with trailing line break check, on 1.x line only
                if (!$tokens[$index - 1]->isComment() || false === strpos($tokens[$index - 1]->getContent(), "\n")) {
                    $tokens[$index]->setContent(' ');
                }
            }

            return;
        }

        $indexChange = $after ? 0 : 1;
        $tokens->insertAt($index + $indexChange, new Token(array(T_WHITESPACE, ' ', $tokens[$index]->getLine())));
    }
}
