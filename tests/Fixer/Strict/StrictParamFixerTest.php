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

namespace PhpCsFixer\Tests\Fixer\Strict;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Strict\StrictParamFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Strict\StrictParamFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class StrictParamFixerTest extends AbstractFixerTestCase
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
            '<?php
    in_array(1, $a, true);
    in_array(1, $a, false);
    in_array(1, $a, $useStrict);',
        ];

        yield [
            '<?php class Foo
                {
                    public function in_array($needle, $haystack) {}
                }',
        ];

        yield [
            '<?php
    in_array(1, $a, true);',
            '<?php
    in_array(1, $a);',
        ];

        yield [
            '<?php
    in_array(1, foo(), true);',
            '<?php
    in_array(1, foo());',
        ];

        yield [
            '<?php
    in_array(1, array(1, 2, 3), true);',
            '<?php
    in_array(1, array(1, 2, 3));',
        ];

        yield [
            '<?php
    in_array(1, [1, 2, 3], true);',
            '<?php
    in_array(1, [1, 2, 3]);',
        ];

        yield [
            '<?php
    in_array(in_array(1, [1, in_array(1, [1, 2, 3], true) ? 21 : 22, 3], true) ? 111 : 222, [1, in_array(1, [1, 2, 3], true) ? 21 : 22, 3], true);',
            '<?php
    in_array(in_array(1, [1, in_array(1, [1, 2, 3]) ? 21 : 22, 3]) ? 111 : 222, [1, in_array(1, [1, 2, 3]) ? 21 : 22, 3]);',
        ];

        yield [
            '<?php
    in_Array(1, $a, true);',
            '<?php
    in_Array(1, $a);',
        ];

        yield [
            '<?php
    base64_decode($foo, true);
    base64_decode($foo, false);
    base64_decode($foo, $useStrict);',
        ];

        yield [
            '<?php
    base64_decode($foo, true);',
            '<?php
    base64_decode($foo);',
        ];

        yield [
            '<?php
    array_search($foo, $bar, true);
    array_search($foo, $bar, false);
    array_search($foo, $bar, $useStrict);',
        ];

        yield [
            '<?php
    array_search($foo, $bar, true);',
            '<?php
    array_search($foo, $bar);',
        ];

        yield [
            '<?php
    array_keys($foo);
    array_keys($foo, $bar, true);
    array_keys($foo, $bar, false);
    array_keys($foo, $bar, $useStrict);',
        ];

        yield [
            '<?php
    array_keys($foo, $bar, true);',
            '<?php
    array_keys($foo, $bar);',
        ];

        yield [
            '<?php
    mb_detect_encoding($foo, $bar, true);
    mb_detect_encoding($foo, $bar, false);
    mb_detect_encoding($foo, $bar, $useStrict);',
        ];

        yield [
            '<?php
    mb_detect_encoding($foo, mb_detect_order(), true);',
            '<?php
    mb_detect_encoding($foo);',
        ];

        yield [
            '<?php
    use function in_array;

    class Foo
    {
        public function __construct($foo, $bar) {}
    }',
        ];

        yield [
            '<?php
    namespace Foo {
        array_keys($foo, $bar, true);
    }
    namespace Bar {
        use function Foo\LoremIpsum;
        array_keys($foo, $bar, true);
    }',
            '<?php
    namespace Foo {
        array_keys($foo, $bar);
    }
    namespace Bar {
        use function Foo\LoremIpsum;
        array_keys($foo, $bar);
    }',
        ];

        yield [
            '<?php
    use function \base64_decode;
    foo($bar);',
        ];

        yield [
            '<?php
    use function Baz\base64_decode;
    foo($bar);',
        ];

        yield [
            '<?php
    in_array(1, foo(), true /* 1 *//* 2 *//* 3 */);',
            '<?php
    in_array(1, foo() /* 1 *//* 2 *//* 3 */);',
        ];

        yield [
            '<?php in_array($b, $c, true, );',
            '<?php in_array($b, $c, );',
        ];

        yield [
            '<?php in_array($b, $c/* 0 *//* 1 */, true,/* 2 *//* 3 */);',
            '<?php in_array($b, $c/* 0 *//* 1 */,/* 2 *//* 3 */);',
        ];
    }
}
