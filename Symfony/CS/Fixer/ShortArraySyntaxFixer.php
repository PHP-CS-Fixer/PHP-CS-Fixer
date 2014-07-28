<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ShortArraySyntaxFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0, $c = $tokens->count(); $index < $c; $index++) {
            $token = $tokens[$index];

            if (Tokens::isKeyword($token) && T_ARRAY === $token[0] && '(' === $tokens->getNextNonWhitespace($index)) {
                $this->fixArray($tokens, $index);
                continue;
            }
        }

        return $tokens->generateCode();
    }

    private function fixArray(Tokens $tokens, &$index)
    {
        $bracesLevel = 0;

        unset($tokens[$index]);
        $index++;

        for ($c = $tokens->count(); $index < $c; $index++) {
            $token = $tokens[$index];

            if ('(' === $token) {
                if (0 === $bracesLevel) {
                    $tokens[$index] = '[';
                }

                ++$bracesLevel;
                continue;
            }

            if (Tokens::isKeyword($token) && T_ARRAY === $token[0] && '(' === $tokens->getNextNonWhitespace($index)) {
                $this->fixArray($tokens, $index);
                continue;
            }

            if (')' === $token) {
                --$bracesLevel;

                if (0 === $bracesLevel) {
                    $tokens[$index] = ']';
                    break;
                }
            }
        }
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
        return 'short_array_syntax';
    }

    public function getDescription()
    {
        return 'PHP array\'s should use the PHP 5.4 short-syntax';
    }
}
