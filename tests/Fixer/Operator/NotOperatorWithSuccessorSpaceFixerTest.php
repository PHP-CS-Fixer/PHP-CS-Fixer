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
 * @author Javier Spagnoletti <phansys@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer
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

    public static function provideFixCases(): array
    {
        return [
            [
                '<?php $i = 0; $i++; $foo = ! false || (! true || ! ! false && (2 === (7 -5)));',
                '<?php $i = 0; $i++; $foo = !false || (!true || !!false && (2 === (7 -5)));',
            ],
            [
                '<?php $i = 0; $i--; $foo = ! false || ($i && ! true);',
                '<?php $i = 0; $i--; $foo = !false || ($i && !true);',
            ],
            [
                '<?php $i = 0; $i--; $foo = ! false || ($i && ! /* some comment */true);',
                '<?php $i = 0; $i--; $foo = !false || ($i && !/* some comment */true);',
            ],
            [
                '<?php $i = 0; $i--; $foo = ! false || ($i && ! true);',
                '<?php $i = 0; $i--; $foo = !false || ($i && !    true);',
            ],
            [
                '<?php $i = 0; $i--; $foo = ! false || ($i && ! /* some comment */ true);',
                '<?php $i = 0; $i--; $foo = !false || ($i && !  /* some comment */ true);',
            ],
            'comment case' => [
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
            ],
        ];
    }
}
