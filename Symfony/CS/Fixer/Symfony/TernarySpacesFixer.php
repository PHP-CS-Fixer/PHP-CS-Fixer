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
use Symfony\CS\Token;
use Symfony\CS\Tokens;

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

            if ('?' === $token->content) {
                ++$ternaryLevel;

                $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($index);
                $nextNonWhitespaceToken = $tokens[$nextNonWhitespaceIndex];

                if (!$nextNonWhitespaceToken->isArray() && ':' === $nextNonWhitespaceToken->content) {
                    // for `$a ?: $b` remove spaces between `?` and `:`
                    if ($tokens[$index + 1]->isWhitespace()) {
                        $tokens[$index + 1]->clear();
                    }
                } else {
                    // for `$a ? $b : $c` ensure space after `?`
                    $this->ensureWhitespaceExistance($tokens, $index + 1, true);
                }

                // for `$a ? $b : $c` ensure space before `?`
                $this->ensureWhitespaceExistance($tokens, $index - 1, false);

                continue;
            }

            if ($ternaryLevel && ':' === $token->content) {
                // for `$a ? $b : $c` ensure space after `:`
                $this->ensureWhitespaceExistance($tokens, $index + 1, true);

                $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

                if ($prevNonWhitespaceToken->isArray() || '?' !== $prevNonWhitespaceToken->content) {
                    // for `$a ? $b : $c` ensure space before `:`
                    $this->ensureWhitespaceExistance($tokens, $index - 1, false);
                }

                --$ternaryLevel;
            }
        }

        return $tokens->generateCode();
    }

    private function ensureWhitespaceExistance(Tokens $tokens, $index, $after)
    {
        $indexChange = $after ? 0 : 1;
        $token = $tokens[$index];

        if ($token->isWhitespace()) {
            return;
        }

        $tokens->insertAt($index + $indexChange, new Token(array(T_WHITESPACE, ' ', $token->line)));
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Standardize spaces around ternary operator.';
    }
}
