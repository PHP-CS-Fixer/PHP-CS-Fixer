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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\NoUnneededImportAliasFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Import\NoUnneededImportAliasFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoUnneededImportAliasFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php use some\ns\{ClassA, ClassB, ClassC  };',
            '<?php use some\ns\{ClassA, ClassB, ClassC as ClassC};',
        ];

        yield [
            '<?php
                use A\B\C  , D\E\F as G;
                use const X\Y\Z as Z1, U\V\W  ;
                use function U\V\W\FX  , U\V\W\FY  ;
            ',
            '<?php
                use A\B\C as C, D\E\F as G;
                use const X\Y\Z as Z1, U\V\W as W;
                use function U\V\W\FX as FX, U\V\W\FY as FY;
            ',
        ];

        yield [
            '<?php
use F  ;
use X as x;
use const CA  ;
use function FW  ;
            ',
            '<?php
use F as F;
use X as x;
use const CA as CA;
use function FW as FW;
            ',
        ];

        yield [
            '<?php
use /* 1 */\F  ;
use const \CA/* 2 */  /* 3 */;
use /* 4 */ function/* 5 */  \FW /* 6 */  /* 7 */ ;
            ',
            '<?php
use /* 1 */\F as F;
use const \CA/* 2 */ as CA/* 3 */;
use /* 4 */ function/* 5 */  \FW /* 6 */ as /* 7 */ FW;
            ',
        ];

        yield [
            '<?php
use \F\B\C  ;
use const \X\Y\CA  ;
use function \U\V\FW  ;
            ',
            '<?php
use \F\B\C as C;
use const \X\Y\CA as CA;
use function \U\V\FW as FW;
            ',
        ];

        yield [
            '<?php use A\B   ?> X <?php use C\D; use E\F   ?>',
            '<?php use A\B as B ?> X <?php use C\D; use E\F as F ?>',
        ];

        yield [
            '<?php use A\B   ?>',
            '<?php use A\B as B ?>',
        ];

        yield [
            '<?php foreach ($a as $a) {}',
        ];
    }
}
