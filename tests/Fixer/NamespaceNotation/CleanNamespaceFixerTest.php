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

namespace PhpCsFixer\Tests\Fixer\NamespaceNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\CleanNamespaceFixer
 */
final class CleanNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP <8.0
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php use function FooLibrary\Bar\Baz\ClassA as Foo ?>',
            '<?php use function FooLibrary \ Bar \ Baz \ /* A */ ClassA as Foo ?>',
        ];

        yield [
            '<?php use function FooLibrary\Bar\Baz\ClassA as Foo;',
            '<?php use function FooLibrary \ Bar \ Baz \ /* A */ ClassA as Foo;',
        ];

        yield [
            '<?php namespace AT\B # foo 3
;',
            '<?php namespace AT # foo 1
\ /**
2
*/
B # foo 3
;',
        ];

        yield [
            '<?php namespace AX\B;',
            '<?php namespace AX /* foo */ \ B;',
        ];

        yield [
            '<?php namespace A1\B\C  ; // foo',
            '<?php namespace A1    \   B   \   C  ; // foo',
        ];

        yield [
            '<?php echo A\B();A\B();A\B();A\B();',
            '<?php echo A \ B();A \ B();A \ B();A \ B();',
        ];

        yield [
            '<?php namespace A\B ?>',
            '<?php namespace A \ B ?>',
        ];

        yield [
            '<?php echo /* */ x\y() ?>',
            '<?php echo /* */ x \ y() ?>',
        ];

        yield [
            '<?php namespace A\B\C\D;?>',
            '<?php namespace A\/* 1 */B/* 2 */\C/* 3 *//* 4 */\/* 5 */D;?>',
        ];

        yield [
            '<?php namespace A\B ?>',
            '<?php namespace A \/* 1 */ B ?>',
        ];

        yield [
            '<?php echo A\B(1, 2, 3) ?>',
            '<?php echo A \ /* 1 */ B(1, 2, 3) ?>',
        ];

        yield [
            '<?php
                namespace A\B\C\D\E /* 5.1 */{ }
                namespace B\B\C\D\E /* 5.2 */{ }
                namespace C\B\C\D\E /* 5.3 */{ }
                namespace D\B\C\D\E /* 5.4 */{ }
                namespace E\B\C\D\E /* 5.5 */{ }
            ',
            '<?php
                namespace A /* 1 */ \ B \   /** 2 */ C \ /* 3 */ D   /* 4 */ \ E /* 5.1 */{ }
                namespace B /* 1 */ \ B \      /* 2 */ C \ /** 3 */ D /* 4 */ \ E /* 5.2 */{ }
                namespace C /* 1 */ \ B \  /* 2 */ C \ /* 3 */ D /** 4 */ \ E /* 5.3 */{ }
                namespace D /* 1 */ \ B \ /* 2 */ C \    /* 3 */ D /* 4 */ \ E /* 5.4 */{ }
                namespace E /* 1 */ \ B \ /* 2 */ C \ /* 3 */ D /* 4 */ \ E /* 5.5 */{ }
            ',
        ];

        yield [
            '<?php
namespace A\B;

try {
    foo();
} catch ( \ParseError\A\B      $e) {
}

if (
    !a instanceof \EventDispatcher\EventDispatcherInterface
) {
    foo();
}
            ',
            '<?php
namespace A \ B;

try {
    foo();
} catch ( \ ParseError\ A \ B      $e) {
}

if (
    !a instanceof \EventDispatcher\/* 1 */EventDispatcherInterface
) {
    foo();
}
            ',
        ];

        yield [
            '<?php use function Foo\iter\ { range, map, filter, apply, reduce, foo\operator };
class Foo
{
    private function foo1(): \Exception\A /** 2 */   // trailing comment
    {
    }

    private function foo2(): \Exception // trailing comment
    {
    }
}',
            '<?php use function Foo \ iter /* A */ \ { range, map, filter, apply, reduce, foo \ operator };
class Foo
{
    private function foo1(): \Exception   \ /* 1 */  A /** 2 */   // trailing comment
    {
    }

    private function foo2(): \Exception // trailing comment
    {
    }
}',
        ];
    }
}
