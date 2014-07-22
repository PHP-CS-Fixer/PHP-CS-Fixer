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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ElseifFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] elseif, not else if
        return preg_replace('/} else +if \(/', '} elseif (', $content);
    }

    public function getLevel()
    {
        // defined in PSR2 ¶5.1
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        // should be run after ControlSpacesFixer
        return -20;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'elseif';
    }

    public function getDescription()
    {
        return 'The keyword elseif should be used instead of else if so that all control keywords looks like single words.';
    }
}
