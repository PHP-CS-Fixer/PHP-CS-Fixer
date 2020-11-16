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
     * @param string $expected
     * @param string $input
     *
     * @requires PHP <8.0
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
            '<?php namespace AT\B
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
                namespace A\B\C\D\E { }
                namespace B\B\C\D\E { }
                namespace C\B\C\D\E { }
                namespace D\B\C\D\E { }
                namespace E\B\C\D\E { }
            ',
            '<?php
                namespace A /* 1 */ \ B \   /** 2 */ C \ /* 3 */ D   /* 4 */ \ E /* 5 */{ }
                namespace B /* 1 */ \ B \      /* 2 */ C \ /** 3 */ D /* 4 */ \ E /* 5 */{ }
                namespace C /* 1 */ \ B \  /* 2 */ C \ /* 3 */ D /** 4 */ \ E /* 5 */{ }
                namespace D /* 1 */ \ B \ /* 2 */ C \    /* 3 */ D /* 4 */ \ E /* 5 */{ }
                namespace E /* 1 */ \ B \ /* 2 */ C \ /* 3 */ D /* 4 */ \ E /* 5 */{ }
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

        if (\PHP_VERSION_ID >= 70000) {
            yield [
                '<?php use function Foo\iter\ { range, map, filter, apply, reduce, foo\operator };',
                '<?php use function Foo \ iter /* A */ \ { range, map, filter, apply, reduce, foo \ operator };',
            ];
        }
    }
}
