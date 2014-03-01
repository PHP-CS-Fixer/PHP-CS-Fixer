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
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
class ObjectOperatorFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] there should not be space before or after T_OBJECT_OPERATOR
        $content = preg_replace('/([^\s]) ->/', '${1}->', $content);
        $content = preg_replace('/-> /', '->', $content);

        return $content;
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
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'object_operator';
    }

    public function getDescription()
    {
        return 'There should not be space before or after object T_OBJECT_OPERATOR.';
    }
}
