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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer
 */
final class NativeFunctionTypeDeclarationCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
class Foo
{
    private function Bar(array $bar) {
        return false;
    }
}
',
                '<?php
class Foo
{
    private function Bar(ARRAY $bar) {
        return false;
    }
}
',
            ],
            [
                '<?php
interface Foo
{
    public function Bar(array $bar);
}
',
                '<?php
interface Foo
{
    public function Bar(ArrAY $bar);
}
',
            ],
            [
                '<?php
function Foo(/**/array/**/$bar) {
    return false;
}
',
                '<?php
function Foo(/**/ARRAY/**/$bar) {
    return false;
}
',
            ],
            [
                '<?php
function Foo(array $a, callable $b, self $c) {}
                ',
                '<?php
function Foo(ARRAY $a, CALLABLE $b, Self $c) {}
                ',
            ],
            [
                '<?php
function Foo(INTEGER $a) {}
                ',
            ],
        ];
    }

    /**
     * @requires PHP <7.0
     *
     * @param string $expected
     *
     * @dataProvider provideFixPre70Cases
     */
    public function testFixPre70($expected)
    {
        $this->doTest($expected);
    }

    public function provideFixPre70Cases()
    {
        return [
            ['<?php function Foo(BOOL $A, FLOAT $B, INT $C, STRING $D, ITERABLE $E, VOID $F, OBJECT $o) {}'],
            ['<?php class Foo { public function Foo(\INT $a) {}}'],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return [
            [
                '<?php final class Foo1 { final public function Foo(bool $A, float $B, int $C, string $D): int {} }',
                '<?php final class Foo1 { final public function Foo(BOOL $A, FLOAT $B, INT $C, STRING $D): INT {} }',
            ],
            [
                '<?php function Foo(bool $A, float $B, int $C, string $D): int {}',
                '<?php function Foo(BOOL $A, FLOAT $B, INT $C, STRING $D): INT {}',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFix71Cases
     * @requires PHP 7.1
     */
    public function testFix71($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix71Cases()
    {
        return [
            [
                '<?php trait XYZ { function Foo(iterable $A): void {} }',
                '<?php trait XYZ { function Foo(ITERABLE $A): VOID {} }',
            ],
            [
                '<?php function Foo(iterable $A): void {}',
                '<?php function Foo(ITERABLE $A): VOID {}',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFix72Cases
     * @requires PHP 7.2
     */
    public function testFix72($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix72Cases()
    {
        return [
            [
                '<?php function Foo(object $A): void {}',
                '<?php function Foo(OBJECT $A): VOID {}',
            ],
        ];
    }
}
