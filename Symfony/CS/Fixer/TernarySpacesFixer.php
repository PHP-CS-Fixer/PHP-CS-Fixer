<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TernarySpacesFixer implements FixerInterface
{
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

                $nextNonWhitespaceIndex = null;
                $nextNonWhitespaceToken = $tokens->getNextNonWhitespace($index, array(), $nextNonWhitespaceIndex);

                if (!$nextNonWhitespaceToken->isArray() && ':' === $nextNonWhitespaceToken->content) {
                    // for `$a ?: $b` remove spaces between `?` and `:`
                    for ($i = $index + 1; $i < $nextNonWhitespaceIndex; ++$i) {
                        $tokens[$i]->clear();
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

                $prevNonWhitespaceToken = $tokens->getPrevNonWhitespace($index);

                if ($prevNonWhitespaceToken->isArray() || '?' !== $prevNonWhitespaceToken->content) {
                    // for `$a ? $b : $c` ensure space before `:`
                    $this->ensureWhitespaceExistance($tokens, $index - 1, false);
                }

                --$ternaryLevel;
            }
        }

        return $tokens->generateCode();
    }

    private function ensureWhitespaceExistance(&$tokens, $index, $after)
    {
        $indexChange = $after ? 0 : 1;
        $token = $tokens[$index];

        if ($token->isWhitespace()) {
            return;
        }

        $tokens->insertAt($index + $indexChange, new Token(array(T_WHITESPACE, ' ', $token->line, )));
    }

    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'ternary_spaces';
    }

    public function getDescription()
    {
        return 'Standardize spaces around ternary operator.';
    }
}
