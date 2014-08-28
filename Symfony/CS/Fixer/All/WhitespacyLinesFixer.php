<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\FixerInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class WhitespacyLinesFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        return preg_replace('/^\h+$/m', '', $content);
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
        return 'whitespacy_lines';
    }

    public function getDescription()
    {
        return 'Remove trailing whitespace at the end of lines.';
    }
}
