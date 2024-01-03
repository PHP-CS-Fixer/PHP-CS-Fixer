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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\OctalNotationFixer
 *
 * @requires PHP 8.1
 */
final class OctalNotationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
                $a = 0;
                $b = \'0\';
                $foo = 0b01;
                $foo = 0x01;
                $foo = 0B01;
                $foo = 0X01;
                $foo = 0b0;
                $foo = 0x0;
                $foo = 0B0;
                $foo = 0X0;
                $foo = 1;
                $foo = 10;
            ',
        ];

        yield [
            '<?php $foo = 0o0000;',
            '<?php $foo = 00000;',
        ];

        yield [
            '<?php
                $foo = 0o123;
                $foo = 0o1;
            ',
            '<?php
                $foo = 0123;
                $foo = 01;
            ',
        ];

        yield [
            '<?php $foo = 0o1_234;',
            '<?php $foo = 01_234;',
        ];

        yield [
            '<?php $foo = +0o1_234;',
            '<?php $foo = +01_234;',
        ];

        yield [
            '<?php $foo = -0o123_456;',
            '<?php $foo = -0_123_456;',
        ];
    }
}
