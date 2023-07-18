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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer
 */
final class TypeDeclarationSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array<int, null|string>>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php function foo(bool /**bla bla*/$param) {}',
            '<?php function foo(bool/**bla bla*/$param) {}',
        ];

        yield [
            '<?php function foo(bool /**bla bla*/$param) {}',
            '<?php function foo(bool  /**bla bla*/$param) {}',
        ];

        yield [
            '<?php function foo(callable $param) {}',
            '<?php function foo(callable$param) {}',
        ];

        yield [
            '<?php function foo(callable $param) {}',
            '<?php function foo(callable  $param) {}',
        ];

        yield [
            '<?php function foo(array &$param) {}',
            '<?php function foo(array&$param) {}',
        ];

        yield [
            '<?php function foo(array &$param) {}',
            '<?php function foo(array  &$param) {}',
        ];

        yield [
            '<?php function foo(array & $param) {}',
            '<?php function foo(array& $param) {}',
        ];

        yield [
            '<?php function foo(array & $param) {}',
            '<?php function foo(array  & $param) {}',
        ];

        yield [
            '<?php function foo(Bar $param) {}',
            '<?php function foo(Bar$param) {}',
        ];

        yield [
            '<?php function foo(Bar $param) {}',
            '<?php function foo(Bar  $param) {}',
        ];

        yield [
            '<?php function foo(Bar\Baz $param) {}',
            '<?php function foo(Bar\Baz$param) {}',
        ];

        yield [
            '<?php function foo(Bar\Baz $param) {}',
            '<?php function foo(Bar\Baz  $param) {}',
        ];

        yield [
            '<?php function foo(Bar\Baz &$param) {}',
            '<?php function foo(Bar\Baz&$param) {}',
        ];

        yield [
            '<?php function foo(Bar\Baz &$param) {}',
            '<?php function foo(Bar\Baz  &$param) {}',
        ];

        yield [
            '<?php function foo(Bar\Baz & $param) {}',
            '<?php function foo(Bar\Baz& $param) {}',
        ];

        yield [
            '<?php function foo(Bar\Baz & $param) {}',
            '<?php function foo(Bar\Baz  & $param) {}',
        ];

        yield [
            '<?php $foo = function(Bar\Baz $param) {};',
            '<?php $foo = function(Bar\Baz$param) {};',
        ];

        yield [
            '<?php $foo = function(Bar\Baz $param) {};',
            '<?php $foo = function(Bar\Baz  $param) {};',
        ];

        yield [
            '<?php $foo = function(Bar\Baz &$param) {};',
            '<?php $foo = function(Bar\Baz&$param) {};',
        ];

        yield [
            '<?php $foo = function(Bar\Baz &$param) {};',
            '<?php $foo = function(Bar\Baz  &$param) {};',
        ];

        yield [
            '<?php $foo = function(Bar\Baz & $param) {};',
            '<?php $foo = function(Bar\Baz& $param) {};',
        ];

        yield [
            '<?php $foo = function(Bar\Baz & $param) {};',
            '<?php $foo = function(Bar\Baz  & $param) {};',
        ];

        yield [
            '<?php class Test { public function foo(Bar\Baz $param) {} }',
            '<?php class Test { public function foo(Bar\Baz$param) {} }',
        ];

        yield [
            '<?php class Test { public function foo(Bar\Baz $param) {} }',
            '<?php class Test { public function foo(Bar\Baz  $param) {} }',
        ];

        yield [
            '<?php $foo = function(array $a,
                    array $b, array $c, array $d) {};',
            '<?php $foo = function(array $a,
                    array$b, array     $c, array
                    $d) {};',
        ];

        yield [
            '<?php $foo = fn(Bar\Baz $param) => null;',
            '<?php $foo = fn(Bar\Baz$param) => null;',
        ];

        yield [
            '<?php $foo = fn(Bar\Baz $param) => null;',
            '<?php $foo = fn(Bar\Baz  $param) => null;',
        ];

        yield [
            '<?php $foo = fn(Bar\Baz &$param) => null;',
            '<?php $foo = fn(Bar\Baz&$param) => null;',
        ];

        yield [
            '<?php $foo = fn(Bar\Baz &$param) => null;',
            '<?php $foo = fn(Bar\Baz  &$param) => null;',
        ];

        yield [
            '<?php $foo = fn(Bar\Baz & $param) => null;',
            '<?php $foo = fn(Bar\Baz& $param) => null;',
        ];

        yield [
            '<?php $foo = fn(Bar\Baz & $param) => null;',
            '<?php $foo = fn(Bar\Baz  & $param) => null;',
        ];

        yield [
            '<?php $foo = fn(array $a,
                    array $b, array $c, array $d) => null;',
            '<?php $foo = fn(array $a,
                    array$b, array     $c, array
                    $d) => null;',
        ];

        yield [
            '<?php function foo(array ...$param) {}',
            '<?php function foo(array...$param) {}',
        ];

        yield [
            '<?php function foo(array & ...$param) {}',
            '<?php function foo(array& ...$param) {}',
        ];

        yield [
            '<?php class Foo { public int $x; }',
            '<?php class Foo { public int$x; }',
        ];

        yield [
            '<?php class Foo { public bool $x; }',
            '<?php class Foo { public bool    $x; }',
        ];

        yield [
            '<?php class Foo { protected \Bar\Baz $c; }',
            '<?php class Foo { protected \Bar\Baz$c; }',
        ];

        yield [
            '<?php class Foo { protected \Bar\Baz $c; }',
            '<?php class Foo { protected \Bar\Baz   $c; }',
        ];

        yield [
            '<?php class Foo { private array $x; }',
            '<?php class Foo { private array$x; }',
        ];

        yield [
            '<?php class Foo { private array $x; }',
            '<?php class Foo { private array
$x; }',
        ];

        yield [
            '<?php
class Point
{
    public \DateTime $x;
    protected bool $y = true;
    private array $z = [];
    public int $a = 0;
    protected string $b = \'\';
    private float $c = 0.0;
}
',
            '<?php
class Point
{
    public \DateTime    $x;
    protected bool      $y = true;
    private array       $z = [];
    public int          $a = 0;
    protected string    $b = \'\';
    private float       $c = 0.0;
}
',
        ];

        yield [
            '<?php function foo($param) {}',
        ];

        yield [
            '<?php function foo($param1,$param2) {}',
        ];

        yield [
            '<?php function foo(&$param) {}',
        ];

        yield [
            '<?php function foo(& $param) {}',
        ];

        yield [
            '<?php function foo(/**int*/$param) {}',
        ];

        yield [
            '<?php function foo(bool /**bla bla*/ $param) {}',
        ];

        yield [
            '<?php $foo = function(
                    array $a,
                    $b
                ) {};',
        ];

        yield [
            '<?php $foo = function(
                    $a,
                    array $b
                ) {};',
        ];

        yield [
            '<?php function foo(...$param) {}',
        ];

        yield [
            '<?php function foo(&...$param) {}',
        ];

        yield [
            '<?php use function some\test\{fn_a, fn_b, fn_c};',
        ];

        yield [
            '<?php use function some\test\{fn_a, fn_b, fn_c} ?>',
        ];

        yield [
            '<?php $foo = fn(
                    array $a,
                    $b
                ) => null;',
        ];

        yield [
            '<?php $foo = fn(
                    $a,
                    array $b
                ) => null;',
        ];

        yield [
            '<?php class Foo { public $p; }',
        ];

        yield [
            '<?php class Foo { protected /* int */ $a; }',
        ];

        yield [
            '<?php class Foo { private int $a; }',
        ];
    }

    /**
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array<int, string>>
     */
    public static function provideFixPhp80Cases(): iterable
    {
        yield [
            '<?php function foo(mixed $a) {}',
            '<?php function foo(mixed$a) {}',
        ];

        yield [
            '<?php function foo(mixed $a) {}',
            '<?php function foo(mixed    $a) {}',
        ];

        yield [
            '<?php
class Foo
{
    public function __construct(
        public int $a,
        protected bool $b,
        private Bar\Baz $c,
    ) {}
}
',
            '<?php
class Foo
{
    public function __construct(
        public int  $a,
        protected bool$b,
        private Bar\Baz     $c,
    ) {}
}
',
        ];
    }

    /**
     * @dataProvider provideFixPhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testFixPhp81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array<int, string>>
     */
    public static function provideFixPhp81Cases(): iterable
    {
        yield [
            '<?php class Foo { private readonly int $bar; }',
            '<?php class Foo { private readonly int$bar; }',
        ];

        yield [
            '<?php class Foo { private readonly int $bar; }',
            '<?php class Foo { private readonly int    $bar; }',
        ];
    }

    /**
     * @dataProvider provideFixPhp82Cases
     *
     * @requires PHP 8.2
     */
    public function testFixPhp82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array<int, string>>
     */
    public static function provideFixPhp82Cases(): iterable
    {
        yield [
            '<?php class Foo { public (A&B)|C $bar; }',
            '<?php class Foo { public (A&B)|C$bar; }',
        ];

        yield [
            '<?php class Foo { public (A&B)|C $bar; }',
            '<?php class Foo { public (A&B)|C    $bar; }',
        ];
    }
}
