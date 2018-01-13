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

use PhpCsFixer\Tests\TestCase;

if (!class_exists('PHPUnit\Framework\ExpectationFailedException')) {
    class_alias('PHPUnit_Framework_ExpectationFailedException', 'PHPUnit\Framework\ExpectationFailedException');
}

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tests\Test\Constraint\SameStringsConstraint
 */
final class SameStringsConstraintTest extends TestCase
{
    public function testSameStringsConstraintFail()
    {
        $this->expectException(
            'PHPUnit\Framework\ExpectationFailedException'
        );
        $this->expectExceptionMessageRegExp(
            '#^Failed asserting that two strings are identical\.[\n] \#Warning\: Strings contain different line endings\! Debug using remapping \["\\\\r" => "R", "\\\\n" => "N", "\\\\t" => "T"\]\:\n \-N\n \+RN$#'
        );

        $constraint = new SameStringsConstraint("\r\n");
        $constraint->evaluate("\n");
    }
}
