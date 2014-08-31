<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ConcatWithoutSpaces implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $whitespaces = array('whitespaces' => " \t");

        foreach ($tokens as $index => $token) {
            if (!$token->isArray() && '.' === $token->content) {
                $tokens->removeLeadingWhitespace($index, $whitespaces);
                $tokens->removeTrailingWhitespace($index, $whitespaces);
            }
        }

        return $tokens->generateCode();
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
        return true;
    }

    public function getName()
    {
        return 'concat_without_spaces';
    }

    public function getDescription()
    {
        return 'Concatenation should be used without spaces.';
    }
}
