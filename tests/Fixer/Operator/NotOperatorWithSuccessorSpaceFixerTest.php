<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer>
 *
 * @covers \PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer
 *
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NotOperatorWithSuccessorSpaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $i = 0; $i++; $foo = ! false || (! true || ! ! false && (2 === (7 -5)));',
            '<?php $i = 0; $i++; $foo = !false || (!true || !!false && (2 === (7 -5)));',
        ];

        yield [
            '<?php $i = 0; $i--; $foo = ! false || ($i && ! true);',
            '<?php $i = 0; $i--; $foo = !false || ($i && !true);',
        ];

        yield [
            '<?php $i = 0; $i--; $foo = ! false || ($i && ! /* some comment */true);',
            '<?php $i = 0; $i--; $foo = !false || ($i && !/* some comment */true);',
        ];

        yield [
            '<?php $i = 0; $i--; $foo = ! false || ($i && ! true);',
            '<?php $i = 0; $i--; $foo = !false || ($i && !    true);',
        ];

        yield [
            '<?php $i = 0; $i--; $foo = ! false || ($i && ! /* some comment */ true);',
            '<?php $i = 0; $i--; $foo = !false || ($i && !  /* some comment */ true);',
        ];

        yield 'comment case' => [
            '<?php
                $a=#
! #
$b;
                ',
            '<?php
                $a=#
!
#
$b;
                ',
        ];
    }
}
