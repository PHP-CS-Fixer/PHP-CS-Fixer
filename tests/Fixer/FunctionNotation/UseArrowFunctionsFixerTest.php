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
     * @requires PHP <7.4
     */
    public function testDoNotFix()
    {
        $this->doTest('<?php foo(function () { return 1; });');
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     * @requires PHP 7.4
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php foo(function () use ($a, &$b) { return 1; });',
            ],
            [
                '<?php foo(function () { bar(); return 1; });',
            ],
            [
                '<?php foo(fn()=> 1);',
                '<?php foo(function(){return 1;});',
            ],
            [
                '<?php foo(fn()=>$a);',
                '<?php foo(function()use($a){return$a;});',
            ],
            [
                '<?php foo( fn () => 1 );',
                '<?php foo( function () { return 1; } );',
            ],
            [
                '<?php $func = static fn &(array &$a, string ...$b): ?int => 1;',
                '<?php $func = static function &(array &$a, string ...$b): ?int { return 1; };',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                <<<'EXPECTED'
<?php
    foo(function () {// comment
        return 1;
    });
EXPECTED
            ],
            [
                <<<'EXPECTED'
<?php
    foo(function () {
        // comment
        return 1;
    });
EXPECTED
            ],
            [
                <<<'EXPECTED'
<?php
    foo(function () {
        return 1; // comment
    });
EXPECTED
            ],
            [
                <<<'EXPECTED'
<?php
    foo(function () {
        return 1;
        // comment
    });
EXPECTED
            ],
            [
                <<<'EXPECTED'
<?php
    foo(function () {
        return
            1;
    });
EXPECTED
            ],
            [
                <<<'EXPECTED'
<?php
    $func = function (
        $a,
        $b
    ) {
        return 1;
    };
EXPECTED
            ],
            [
                <<<'EXPECTED'
<?php
    $func = function () {
        return function () {
            foo();
        };
    };
EXPECTED
            ],
        ];
    }
}
