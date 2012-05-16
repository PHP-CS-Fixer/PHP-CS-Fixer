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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class EndOfFileLineFeedFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] A file must always ends with a linefeed character
        if (strlen($content) && "\n" != substr($content, -1)) {
            return $content."\n";
        }

        return $content;
    }

    public function supports(\SplFileInfo $file)
    {
        return true;
    }
}
