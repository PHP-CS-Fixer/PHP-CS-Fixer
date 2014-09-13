<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ShortArraySyntaxFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0, $c = $tokens->count(); $index < $c; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_ARRAY) && '(' === $tokens[$tokens->getNextNonWhitespace($index)]->content) {
                $this->fixArray($tokens, $index);
                continue;
            }
        }

        return $tokens->generateCode();
    }

    private function fixArray(Tokens $tokens, &$index)
    {
        $tokens[$index]->clear();
        $bracesLevel = 0;
        ++$index;

        for ($c = $tokens->count(); $index < $c; ++$index) {
            $token = $tokens[$index];

            if ('(' === $token->content) {
                if (0 === $bracesLevel) {
                    $tokens[$index]->content = '[';
                }

                ++$bracesLevel;
                continue;
            }

            if ($token->isGivenKind(T_ARRAY) && '(' === $tokens[$tokens->getNextNonWhitespace($index)]->content) {
                $this->fixArray($tokens, $index);
                continue;
            }

            if (')' === $token->content) {
                --$bracesLevel;

                if (0 === $bracesLevel) {
                    $tokens[$index]->content = ']';
                    break;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP array\'s should use the PHP 5.4 short-syntax.';
    }
}
