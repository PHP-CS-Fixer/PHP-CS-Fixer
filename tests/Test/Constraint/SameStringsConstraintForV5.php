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

namespace PhpCsFixer\Tests\Test\Constraint;

if (!class_exists('PHPUnit\Framework\Constraint\IsIdentical')) {
    class_alias('PHPUnit_Framework_Constraint_IsIdentical', 'PHPUnit\Framework\Constraint\IsIdentical');
}

use PHPUnit\Framework\Constraint\IsIdentical;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class SameStringsConstraintForV5 extends IsIdentical
{
    protected function additionalFailureDescription($other)
    {
        if (
            $other === $this->value
            || preg_replace('/(\r\n|\n\r|\r)/', "\n", $other) !== preg_replace('/(\r\n|\n\r|\r)/', "\n", $this->value)
        ) {
            return '';
        }

        return ' #Warning: Strings contain different line endings! Debug using remapping ["\r" => "R", "\n" => "N", "\t" => "T"]:'
            ."\n"
            .' -'.str_replace(["\r", "\n", "\t"], ['R', 'N', 'T'], $other)
            ."\n"
            .' +'.str_replace(["\r", "\n", "\t"], ['R', 'N', 'T'], $this->value);
    }
}
