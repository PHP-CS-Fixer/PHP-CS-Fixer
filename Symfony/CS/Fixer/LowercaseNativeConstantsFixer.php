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
class LowercaseNativeConstantsFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (Tokens::isNativeConstant($token)) {
                if (
                    is_array($tokens->getPrevNonWhitespace($index, array('whitespaces' => " \t\n", )))
                    ||
                    is_array($tokens->getNextNonWhitespace($index, array('whitespaces' => " \t\n", )))
                ) {
                    continue;
                }

                $tokens[$index] = strtolower($token[1]);
            }
        }

        return $tokens->generateCode();
    }

    public function getLevel()
    {
        // defined in PSR2 ¶2.5
        return FixerInterface::PSR2_LEVEL;
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
        return 'lowercase_constants';
    }

    public function getDescription()
    {
        return 'The PHP constants true, false, and null MUST be in lower case.';
    }
}
