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
class IndentationFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] Indentation is done by steps of four spaces (tabs are never allowed)
        return preg_replace_callback('/^([ \t]+)/m', function ($matches) use ($content) {
            return str_replace("\t", '    ', $matches[0]);
        }, $content);
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == $file->getExtension();
    }
}
