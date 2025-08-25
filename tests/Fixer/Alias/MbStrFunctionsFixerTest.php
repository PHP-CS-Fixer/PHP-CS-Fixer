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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer>
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class MbStrFunctionsFixerTest extends AbstractFixerTestCase
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
        yield ['<?php $x = "strlen";'];

        yield ['<?php $x = Foo::strlen("bar");'];

        yield ['<?php $x = new strlen("bar");'];

        yield ['<?php $x = new \strlen("bar");'];

        yield ['<?php $x = new Foo\strlen("bar");'];

        yield ['<?php $x = Foo\strlen("bar");'];

        yield ['<?php $x = strlen::call("bar");'];

        yield ['<?php $x = $foo->strlen("bar");'];

        yield ['<?php $x = strlen();']; // number of arguments mismatch

        yield ['<?php $x = strlen($a, $b);']; // number of arguments mismatch

        yield ['<?php $x = mb_strlen("bar");', '<?php $x = strlen("bar");'];

        yield ['<?php $x = \mb_strlen("bar");', '<?php $x = \strlen("bar");'];

        yield ['<?php $x = mb_strtolower(mb_strstr("bar", "a"));', '<?php $x = strtolower(strstr("bar", "a"));'];

        yield ['<?php $x = mb_strtolower( \mb_strstr ("bar", "a"));', '<?php $x = strtolower( \strstr ("bar", "a"));'];

        yield ['<?php $x = mb_substr("bar", 2, 1);', '<?php $x = substr("bar", 2, 1);'];

        yield [
            '<?php
                interface Test
                {
                    public function &strlen($a);
                    public function strtolower($a);
                }',
        ];

        yield [
            '<?php $a = mb_str_split($a);',
            '<?php $a = str_split($a);',
        ];

        yield [
            <<<'PHP'
                <?php
                namespace Foo;
                use function Bar\strlen;
                use function mb_strtolower;
                use function mb_strtoupper;
                use function \mb_str_split;
                return strlen($x) > 10 ? mb_strtolower($x) : mb_strtoupper($x);
                PHP,
            <<<'PHP'
                <?php
                namespace Foo;
                use function Bar\strlen;
                use function strtolower;
                use function strtoupper;
                use function \str_split;
                return strlen($x) > 10 ? strtolower($x) : strtoupper($x);
                PHP,
        ];
    }

    /**
     * @requires PHP 8.3
     *
     * @dataProvider provideFix83Cases
     */
    public function testFix83(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, null|string}>
     */
    public static function provideFix83Cases(): iterable
    {
        yield 'mb_str_pad()' => [
            '<?php $x = mb_str_pad("bar", 2, "0", STR_PAD_LEFT);',
            '<?php $x = str_pad("bar", 2, "0", STR_PAD_LEFT);',
        ];
    }

    /**
     * @requires PHP 8.4
     *
     * @dataProvider provideFix84Cases
     */
    public function testFix84(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, null|string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield 'mb_trim 1 argument' => [
            '<?php $x = mb_trim("    foo  ");',
            '<?php $x = trim("    foo  ");',
        ];

        yield 'mb_trim 2 arguments' => [
            '<?php $x = mb_trim("____foo__", "_");',
            '<?php $x = trim("____foo__", "_");',
        ];

        yield 'ltrim' => [
            '<?php $x = mb_ltrim("    foo  ");',
            '<?php $x = ltrim("    foo  ");',
        ];

        yield 'rtrim' => [
            '<?php $x = mb_rtrim("    foo  ");',
            '<?php $x = rtrim("    foo  ");',
        ];
    }
}
