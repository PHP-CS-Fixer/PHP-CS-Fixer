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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\UseArrowFunctionsFixer
 */
final class UseArrowFunctionsFixerTest extends AbstractFixerTestCase
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
            '<?php foo(function () use ($a, &$b) { return 1; });',
        ];

        yield [
            '<?php foo(function () { bar(); return 1; });',
        ];

        yield [
            '<?php foo(fn()=> 1);',
            '<?php foo(function(){return 1;});',
        ];

        yield [
            '<?php foo(fn()=>$a);',
            '<?php foo(function()use($a){return$a;});',
        ];

        yield [
            '<?php foo( fn () => 1 );',
            '<?php foo( function () { return 1; } );',
        ];

        yield [
            '<?php $func = static fn &(array &$a, string ...$b): ?int => 1;',
            '<?php $func = static function &(array &$a, string ...$b): ?int { return 1; };',
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(1, fn (int $a, Foo $b) => bar($a, $c), 2);
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                    foo(1, function (int $a, Foo $b) use ($c, $d) {
                        return bar($a, $c);
                    }, 2);
                INPUT
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(fn () => 1);
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                    foo(function () {


                        return 1;


                    });
                INPUT
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(fn ($a) => fn () => $a + 1);
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                    foo(function ($a) {
                        return function () use ($a) {
                            return $a + 1;
                        };
                    });
                INPUT
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {// comment
                        return 1;
                    });
                EXPECTED
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {
                        // comment
                        return 1;
                    });
                EXPECTED
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {
                        return 1; // comment
                    });
                EXPECTED
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {
                        return 1;
                        // comment
                    });
                EXPECTED
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    foo(function () {
                        return
                            1;
                    });
                EXPECTED
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    $func = function (
                        $a,
                        $b
                    ) {
                        return 1;
                    };
                EXPECTED
        ];

        yield [
            <<<'EXPECTED'
                <?php
                    $func = function () {
                        return function () {
                            foo();
                        };
                    };
                EXPECTED
        ];

        yield [
            '<?php $testDummy = fn () => null;',
            '<?php $testDummy = function () { return; };',
        ];

        yield [
            '<?php $testDummy = fn () => null ;',
            '<?php $testDummy = function () { return ; };',
        ];

        yield [
            '<?php $testDummy = fn () => null/* foo */;',
            '<?php $testDummy = function () { return/* foo */; };',
        ];
    }
}
