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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\IntegerLiteralCaseFixer
 */
final class IntegerLiteralCaseFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): \Generator
    {
        yield [
            '<?php $foo1 = 0xFF; $foo2 = 0xDEFA; $foo3 = 0xFA; $foo4 = 0xFA;',
            '<?php $foo1 = 0XFF; $foo2 = 0xdefa; $foo3 = 0Xfa; $foo4 = 0xFA;',
        ];

        yield [
            '<?php $foo = 0xA1FB20;',
            '<?php $foo = 0xa1fb20;',
        ];

        yield [
            '<?php $foo = 0b1101;',
            '<?php $foo = 0B1101;',
        ];
    }

    /**
     * @dataProvider provideFix74Cases
     * @requires PHP 7.4
     */
    public function testFix74(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix74Cases(): \Generator
    {
        yield [
            '<?php $A = 1_234_567;',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): \Generator
    {
        yield [
            '<?php $foo = 0o123;',
            '<?php $foo = 0O123;',
        ];
    }
}
