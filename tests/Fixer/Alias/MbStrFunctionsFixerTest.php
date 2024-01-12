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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 * @covers \PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer
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
            <<<'EOD'
                <?php
                                interface Test
                                {
                                    public function &strlen($a);
                                    public function strtolower($a);
                                }
                EOD,
        ];

        yield [
            '<?php $a = mb_str_split($a);',
            '<?php $a = str_split($a);',
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
}
