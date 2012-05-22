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
class IndentationFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] Indentation is done by steps of four spaces (tabs are never allowed)
        return preg_replace_callback('/^([ \t]+)/m', function ($matches) use ($content) {
            return str_replace("\t", '    ', $matches[0]);
        }, $content);
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
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'indentation';
    }

    public function getDescription()
    {
        return 'Code must use 4 spaces for indenting, not tabs.';
    }
}
