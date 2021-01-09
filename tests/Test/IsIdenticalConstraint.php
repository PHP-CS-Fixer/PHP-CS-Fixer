<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\PhpunitConstraintIsIdenticalString\Constraint\IsIdenticalString;

/**
 * @internal
 *
 * @todo Remove me when usages will end up in dedicated package.
 */
trait IsIdenticalConstraint
{
    /**
     * @todo Remove me when this class will end up in dedicated package.
     *
     * @param string $expected
     *
     * @return IsIdenticalString
     */
    private static function createIsIdenticalStringConstraint($expected)
    {
        return new IsIdenticalString($expected);
    }
}
