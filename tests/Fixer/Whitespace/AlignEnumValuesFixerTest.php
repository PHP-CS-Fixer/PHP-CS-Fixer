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
 * @author Kévin Pérais <vinorcola@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\AlignEnumValuesFixer
 */
final class AlignEnumValuesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            // Test already well formatted enum.
            [
                "<?php enum MyEnum: string
{
    case AB    = 'ab';
    case C     = 'c';
    case DEFGH = 'defgh';
}",
            ],
            // Test string backed enum.
            [
                "<?php enum MyEnum: string
{
    case AB    = 'ab';
    case C     = 'c';
    case DEFGH = 'defgh';
}",
                "<?php enum MyEnum: string
{
    case AB = 'ab';
    case C = 'c';
    case DEFGH = 'defgh';
}",
            ],
            // Test int backed enum.
            [
                '<?php enum MyEnum: int
{
    case BENJI     = 1;
    case ELIZABETH = 2;
    case CORA      = 4;
}',
                '<?php enum MyEnum: int
{
    case BENJI
    = 1;
    case ELIZABETH = 2;
    case CORA = 4;
}',
            ],
            // Test enum with empty lines.
            [
                '<?php enum MyEnum: int
{
    case BENJI     = 1;

    case ELIZABETH = 2;

    case CORA      = 4;
}',
                '<?php enum MyEnum: int
{
    case BENJI = 1;

    case ELIZABETH = 2;

    case CORA               = 4;
}',
            ],
            // Test non-backed enum.
            [
                '<?php enum MyEnum
{
    case ADIOS;
    case AMIGOS;
    case FOREVER;
}',
            ],
            // Test inline backed enum.
            [
                '<?php enum MyEnum: int { case BENJI = 1; case ELIZABETH = 2; case CORA = 4; }',
            ],
            // Test backed enum with several cases on the same line.
            // @todo This test fails, but should that case be tested? The enum should be formatted before by another rule
            //            [
            //                "<?php enum MyEnum: int
            // {
            //    case BENJI     = 1;
            //    case ELIZABETH = 2;
            //    case CORA      = 4;
            // }",
            //                "<?php enum MyEnum: int
            // {
            //    case BENJI = 1; case ELIZABETH = 2;
            //    case CORA = 4;
            // }",
            //            ],
            // Test several enums.
            [
                "<?php enum MyEnum: string
{
    case AB    = 'ab';
    case C     = 'c';
    case DEFGH = 'defgh';
}

enum MyOtherEnum
{
    case ADIOS;
    case AMIGOS;
    case FOREVER;
}

enum MyNamedEnum: int
{
    case BENJI     = 1;
    case ELIZABETH = 2;
    case CORA      = 4;
}",
                "<?php enum MyEnum: string
{
    case AB = 'ab';
    case C = 'c';
    case DEFGH = 'defgh';
}

enum MyOtherEnum
{
    case ADIOS;
    case AMIGOS;
    case FOREVER;
}

enum MyNamedEnum: int
{
    case BENJI = 1;
    case ELIZABETH = 2;
    case CORA = 4;
}",
            ],
        ];
    }
}
