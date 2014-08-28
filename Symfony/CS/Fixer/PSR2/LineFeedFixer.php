<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\FixerInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LineFeedFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] Use the linefeed character (0x0A) to end lines
        return str_replace("\r\n", "\n", $content);
    }

    public function getLevel()
    {
        // defined in PSR2 ¶2.2
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 50;
    }

    public function supports(\SplFileInfo $file)
    {
        return true;
    }

    public function getName()
    {
        return 'linefeed';
    }

    public function getDescription()
    {
        return 'All PHP files must use the Unix LF (linefeed) line ending.';
    }
}
