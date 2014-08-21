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

use Symfony\CS\FixerInterface;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ConcatWithSpaces implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isArray() && '.' === $token->content) {
                if (!$tokens[$index + 1]->isWhitespace()) {
                    $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
                }

                if (!$tokens[$index - 1]->isWhitespace()) {
                    $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
                }
            }
        }

        return $tokens->generateCode();
    }

    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }

    public function getPriority()
    {
        // should be run after the ConcatWithSpaces
        return -10;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'concat_with_spaces';
    }

    public function getDescription()
    {
        return 'Concatenation should be used with at least one whitespace around.';
    }
}
