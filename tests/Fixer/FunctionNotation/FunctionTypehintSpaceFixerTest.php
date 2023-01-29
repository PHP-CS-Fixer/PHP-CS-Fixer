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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer
 */
final class FunctionTypehintSpaceFixerTest extends AbstractFixerTestCase
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
                '<?php function foo($param) {}',
            ],
            [
                '<?php function foo($param1,$param2) {}',
            ],
            [
                '<?php function foo(&$param) {}',
            ],
            [
                '<?php function foo(& $param) {}',
            ],
            [
                '<?php function foo(/**int*/$param) {}',
            ],
            [
                '<?php function foo(bool /**bla bla*/ $param) {}',
            ],
            [
                '<?php function foo(bool /**bla bla*/$param) {}',
                '<?php function foo(bool/**bla bla*/$param) {}',
            ],
            [
                '<?php function foo(bool /**bla bla*/$param) {}',
                '<?php function foo(bool  /**bla bla*/$param) {}',
            ],
            [
                '<?php function foo(callable $param) {}',
                '<?php function foo(callable$param) {}',
            ],
            [
                '<?php function foo(callable $param) {}',
                '<?php function foo(callable  $param) {}',
            ],
            [
                '<?php function foo(array &$param) {}',
                '<?php function foo(array&$param) {}',
            ],
            [
                '<?php function foo(array &$param) {}',
                '<?php function foo(array  &$param) {}',
            ],
            [
                '<?php function foo(array & $param) {}',
                '<?php function foo(array& $param) {}',
            ],
            [
                '<?php function foo(array & $param) {}',
                '<?php function foo(array  & $param) {}',
            ],
            [
                '<?php function foo(Bar $param) {}',
                '<?php function foo(Bar$param) {}',
            ],
            [
                '<?php function foo(Bar $param) {}',
                '<?php function foo(Bar  $param) {}',
            ],
            [
                '<?php function foo(Bar\Baz $param) {}',
                '<?php function foo(Bar\Baz$param) {}',
            ],
            [
                '<?php function foo(Bar\Baz $param) {}',
                '<?php function foo(Bar\Baz  $param) {}',
            ],
            [
                '<?php function foo(Bar\Baz &$param) {}',
                '<?php function foo(Bar\Baz&$param) {}',
            ],
            [
                '<?php function foo(Bar\Baz &$param) {}',
                '<?php function foo(Bar\Baz  &$param) {}',
            ],
            [
                '<?php function foo(Bar\Baz & $param) {}',
                '<?php function foo(Bar\Baz& $param) {}',
            ],
            [
                '<?php function foo(Bar\Baz & $param) {}',
                '<?php function foo(Bar\Baz  & $param) {}',
            ],
            [
                '<?php $foo = function(Bar\Baz $param) {};',
                '<?php $foo = function(Bar\Baz$param) {};',
            ],
            [
                '<?php $foo = function(Bar\Baz $param) {};',
                '<?php $foo = function(Bar\Baz  $param) {};',
            ],
            [
                '<?php $foo = function(Bar\Baz &$param) {};',
                '<?php $foo = function(Bar\Baz&$param) {};',
            ],
            [
                '<?php $foo = function(Bar\Baz &$param) {};',
                '<?php $foo = function(Bar\Baz  &$param) {};',
            ],
            [
                '<?php $foo = function(Bar\Baz & $param) {};',
                '<?php $foo = function(Bar\Baz& $param) {};',
            ],
            [
                '<?php $foo = function(Bar\Baz & $param) {};',
                '<?php $foo = function(Bar\Baz  & $param) {};',
            ],
            [
                '<?php class Test { public function foo(Bar\Baz $param) {} }',
                '<?php class Test { public function foo(Bar\Baz$param) {} }',
            ],
            [
                '<?php class Test { public function foo(Bar\Baz $param) {} }',
                '<?php class Test { public function foo(Bar\Baz  $param) {} }',
            ],
            [
                '<?php $foo = function(array $a,
                    array $b, array $c, array $d) {};',
                '<?php $foo = function(array $a,
                    array$b, array     $c, array
                    $d) {};',
            ],
            [
                '<?php $foo = function(
                    array $a,
                    $b
                ) {};',
            ],
            [
                '<?php $foo = function(
                    $a,
                    array $b
                ) {};',
            ],
            [
                '<?php function foo(...$param) {}',
            ],
            [
                '<?php function foo(&...$param) {}',
            ],
            [
                '<?php function foo(array ...$param) {}',
                '<?php function foo(array...$param) {}',
            ],
            [
                '<?php function foo(array & ...$param) {}',
                '<?php function foo(array& ...$param) {}',
            ],
            ['<?php use function some\test\{fn_a, fn_b, fn_c};'],
            ['<?php use function some\test\{fn_a, fn_b, fn_c} ?>'],
            [
                '<?php $foo = fn(Bar\Baz $param) => null;',
                '<?php $foo = fn(Bar\Baz$param) => null;',
            ],
            [
                '<?php $foo = fn(Bar\Baz $param) => null;',
                '<?php $foo = fn(Bar\Baz  $param) => null;',
            ],
            [
                '<?php $foo = fn(Bar\Baz &$param) => null;',
                '<?php $foo = fn(Bar\Baz&$param) => null;',
            ],
            [
                '<?php $foo = fn(Bar\Baz &$param) => null;',
                '<?php $foo = fn(Bar\Baz  &$param) => null;',
            ],
            [
                '<?php $foo = fn(Bar\Baz & $param) => null;',
                '<?php $foo = fn(Bar\Baz& $param) => null;',
            ],
            [
                '<?php $foo = fn(Bar\Baz & $param) => null;',
                '<?php $foo = fn(Bar\Baz  & $param) => null;',
            ],
            [
                '<?php $foo = fn(array $a,
                    array $b, array $c, array $d) => null;',
                '<?php $foo = fn(array $a,
                    array$b, array     $c, array
                    $d) => null;',
            ],
            [
                '<?php $foo = fn(
                    array $a,
                    $b
                ) => null;',
            ],
            [
                '<?php $foo = fn(
                    $a,
                    array $b
                ) => null;',
            ],
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php function foo(mixed $a){}',
            '<?php function foo(mixed$a){}',
        ];
    }
}
