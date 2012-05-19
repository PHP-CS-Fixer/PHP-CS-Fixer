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
 * @author Christophe Coevoet <stof@notk.org>
 */
class ExtraEmptyLinesFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] Duplicated empty lines should not be used.
        return str_replace("\n\n\n", "\n\n", $content);
    }

    public function supports(\SplFileInfo $file)
    {
        return true;
    }
}
