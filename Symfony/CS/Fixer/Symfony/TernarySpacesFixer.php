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
final class TernarySpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(array('?', ':'));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $ternaryLevel = 0;

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
                    $this->ensureWhitespaceExistance($tokens, $index + 1, true);
                }

                // for `$a ? $b : $c` ensure space before `?`
                $this->ensureWhitespaceExistance($tokens, $index - 1, false);

                continue;
            }

            if ($ternaryLevel && $token->equals(':')) {
                // for `$a ? $b : $c` ensure space after `:`
                $this->ensureWhitespaceExistance($tokens, $index + 1, true);

                $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

                if (!$prevNonWhitespaceToken->equals('?')) {
                    // for `$a ? $b : $c` ensure space before `:`
                    $this->ensureWhitespaceExistance($tokens, $index - 1, false);
                }

                --$ternaryLevel;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Standardize spaces around ternary operator.';
    }

    private function ensureWhitespaceExistance(Tokens $tokens, $index, $after)
    {
        $indexChange = $after ? 0 : 1;
        $token = $tokens[$index];

        if ($token->isWhitespace()) {
            return;
        }

        $tokens->insertAt($index + $indexChange, new Token(array(T_WHITESPACE, ' ')));
    }
}
