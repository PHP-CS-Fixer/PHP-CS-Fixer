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
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class IndentationFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!Tokens::isWhitespace($token)) {
                continue;
            }

            $tokenContent = is_array($token) ? $token[1] : $token;
            $tokens[$index] = preg_replace('/(?:(?<! ) {1,3})?\t/', '    ', $tokenContent);
        }

        return $tokens->generateCode();
    }

    public function getLevel()
    {
        // defined in PSR2 ¶2.4
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 50;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'indentation';
    }

    public function getDescription()
    {
        return 'Code MUST use an indent of 4 spaces, and MUST NOT use tabs for indenting.';
    }
}
