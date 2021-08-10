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
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer
 */
final class NativeFunctionTypeDeclarationCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
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
class Bar { function Foo(array $a, callable $b, self $c) {} }
                ',
                '<?php
class Bar { function Foo(ARRAY $a, CALLABLE $b, Self $c) {} }
                ',
            ],
            [
                '<?php
function Foo(INTEGER $a) {}
                ',
            ],
            [
                '<?php function Foo(
                    String\A $x,
                    B\String\C $y
                ) {}',
            ],
            [
                '<?php final class Foo1 { final public function Foo(bool $A, float $B, int $C, string $D): int {} }',
                '<?php final class Foo1 { final public function Foo(BOOL $A, FLOAT $B, INT $C, STRING $D): INT {} }',
            ],
            [
                '<?php function Foo(bool $A, float $B, int $C, string $D): int {}',
                '<?php function Foo(BOOL $A, FLOAT $B, INT $C, STRING $D): INT {}',
            ],
            [
                '<?php function Foo(): Foo\A { return new Foo(); }',
            ],
        ];
    }

    /**
     * @dataProvider provideFix71Cases
     * @requires PHP 7.1
     */
    public function testFix71(string $expected, string $input): void
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
            [
                '<?php function Foo(?int $A): void {}',
                '<?php function Foo(?INT $A): VOID {}',
            ],
            [
                '<?php function Foo(string $A): ?/* */int {}',
                '<?php function Foo(STRING $A): ?/* */INT {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFix72Cases
     * @requires PHP 7.2
     */
    public function testFix72(string $expected, string $input): void
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

    /**
     * @dataProvider provideFix80Cases
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases()
    {
        yield [
            '<?php class T { public function Foo(object $A): static {}}',
            '<?php class T { public function Foo(object $A): StatiC {}}',
        ];

        yield [
            '<?php class T { public function Foo(object $A): ?static {}}',
            '<?php class T { public function Foo(object $A): ?StatiC {}}',
        ];

        yield [
            '<?php class T { public function Foo(mixed $A): mixed {}}',
            '<?php class T { public function Foo(Mixed $A): MIXED {}}',
        ];

        yield [
            '<?php function foo(int|bool $x) {}',
            '<?php function foo(INT|BOOL $x) {}',
        ];

        yield [
            '<?php function foo(int | bool $x) {}',
            '<?php function foo(INT | BOOL $x) {}',
        ];

        yield [
            '<?php function foo(): int|bool {}',
            '<?php function foo(): INT|BOOL {}',
        ];
    }
}
