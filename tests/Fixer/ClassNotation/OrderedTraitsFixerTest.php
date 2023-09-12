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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @covers \PhpCsFixer\Fixer\ClassNotation\OrderedTraitsFixer
 *
 * @internal
 */
final class OrderedTraitsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple' => [
            '<?php
class Foo {
    use A;
    use B;
}',
            '<?php
class Foo {
    use B;
    use A;
}',
        ];

        yield 'in multiple classes' => [
            '<?php
class Foo {
    use A;
    use C;
}
class Bar {
    use B;
    use D;
}',
            '<?php
class Foo {
    use C;
    use A;
}
class Bar {
    use D;
    use B;
}',
        ];

        yield 'separated by a property' => [
            '<?php
class Foo {
    use A;
    use C;

    private $foo;

    use B;
    use D;
}',
            '<?php
class Foo {
    use C;
    use A;

    private $foo;

    use D;
    use B;
}',
        ];

        yield 'separated by a method' => [
            '<?php
class Foo {
    use A;
    use C;

    public function foo() { }

    use B;
    use D;
}',
            '<?php
class Foo {
    use C;
    use A;

    public function foo() { }

    use D;
    use B;
}',
        ];

        yield 'grouped statements' => [
            '<?php
class Foo {
    use A, C;
    use B;
}',
            '<?php
class Foo {
    use B;
    use A, C;
}',
        ];

        yield 'with aliases and conflicts' => [
            '<?php
class Foo {
    use A {
        A::foo insteadof B;
        A::bar as bazA;
        A::baz as protected;
    }
    use B {
        B::bar as bazB;
    }
}',
            '<?php
class Foo {
    use B {
        B::bar as bazB;
    }
    use A {
        A::foo insteadof B;
        A::bar as bazA;
        A::baz as protected;
    }
}',
        ];

        yield 'symbol imports' => [
            '<?php
use C;
use B;
use A;',
        ];

        yield 'anonymous function with inherited variables' => [
            '<?php
$foo = function () use ($b, $a) { };
$bar = function () use ($a, $b) { };',
        ];

        yield 'multiple traits in a single statement' => [
            '<?php
class Foo {
    use A, B, C, D;
}',
            '<?php
class Foo {
    use C, B, D, A;
}',
        ];

        yield 'multiple traits per statement' => [
            '<?php
class Foo {
    use A, D;
    use B, C;
}',
            '<?php
class Foo {
    use C, B;
    use D, A;
}',
        ];

        $uses = [];
        for ($i = 0; $i < 25; ++$i) {
            $uses[] = sprintf('    use A%02d;', $i);
        }

        yield 'simple, multiple I' => [
            sprintf("<?php\nclass Foo {\n%s\n}", implode("\n", $uses)),
            sprintf("<?php\nclass Foo {\n%s\n}", implode("\n", array_reverse($uses))),
        ];

        yield 'simple, length diff. I' => [
            '<?php
class Foo {
    use A;
    use B\B;
    use C\C\C;
    use D\D\D\D;
}',
            '<?php
class Foo {
    use D\D\D\D;
    use C\C\C;
    use B\B;
    use A;
}',
        ];

        yield 'comments handling' => [
            '<?php
class Foo {
    /* A */use A\A\A\A/* A */;
    /* B */use B\B\B/* B */;
    /* C */use C\C/* C */;
    /* D */use D/* D */;
}',
            '<?php
class Foo {
    /* D */use D/* D */;
    /* C */use C\C/* C */;
    /* B */use B\B\B/* B */;
    /* A */use A\A\A\A/* A */;
}',
        ];

        yield 'grouped statements II' => [
            '<?php
class Foo {
    use A\Z, C\Y;
    use B\E;
}',
            '<?php
class Foo {
    use B\E;
    use A\Z, C\Y;
}',
        ];

        yield 'simple, leading \\' => [
            '<?php
class Foo {
    use \A;
    use \B;
}',
            '<?php
class Foo {
    use \B;
    use \A;
}',
        ];

        yield 'simple, leading \\ before character order' => [
            '<?php
class Foo {
    use A;
    use \B;
    use C;
}',
            '<?php
class Foo {
    use C;
    use \B;
    use A;
}',
        ];

        yield 'with phpdoc' => [
            '<?php
class Foo {
    // foo 1

    /** @phpstan-use A<Foo> */
    use A;
    /** @phpstan-use B<Foo> */
    use B;

    /** @phpstan-use C<Foo> */
    use C;
}',
            '<?php
class Foo {
    /** @phpstan-use C<Foo> */
    use C;
    /** @phpstan-use B<Foo> */
    use B;

    // foo 1

    /** @phpstan-use A<Foo> */
    use A;
}',
        ];

        yield 'simple and with namespace' => [
            '<?php

class User
{
    use Test\B, TestA;
}',
            '<?php

class User
{
    use TestA, Test\B;
}',
        ];
    }

    /**
     * @param array<mixed> $configuration
     *
     * @dataProvider provideFixWithConfigurationCases
     */
    public function testFixWithConfiguration(array $configuration, string $expected, ?string $input = null): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<mixed>
     */
    public static function provideFixWithConfigurationCases(): iterable
    {
        yield 'with case sensitive order' => [
            [
                'case_sensitive' => true,
            ],
            '<?php
class Foo {
    use AA;
    use Aaa;
}',
            '<?php
class Foo {
    use Aaa;
    use AA;
}',
        ];
    }
}
