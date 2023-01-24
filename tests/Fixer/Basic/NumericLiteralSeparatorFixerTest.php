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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Marvin Heilemann <marvin.heilemann+github@googlemail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\NumericLiteralSeparatorFixer
 */
final class NumericLiteralSeparatorFixerTest extends AbstractFixerTestCase
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
        $cases = [
            'decimal' => [
                '1234' => '1_234',
                '-1234' => '-1_234',
                '12345' => '12_345',
                '123456' => '123_456',
                '1_2_3_4' => '1_234',
            ],
            'binary' => [
                '0b0101010001101000' => '0b0101_0100_0110_1000',
                '0B01010100_01101000' => '0B0101_0100_0110_1000',
                '0b01010100011010000110010101101111' => '0b0101_0100_0110_1000_0110_0101_0110_1111',
                '0b110001000' => '0b1_1000_1000',
            ],
            'float' => [
                '1234.5' => '1_234.5',
                '1.2345' => '1.234_5',
                '01234.5' => '01_234.5',
                '1234e5' => '1_234e5',
                '1234E5' => '1_234E5',
                '1e2345' => '1e2_345',
                '1234.5678e1234' => '1_234.567_8e1_234',
                '1.1e-1234' => '1.1e-1_234',
                '1.1e-12345' => '1.1e-12_345',
                '1.1e-123456' => '1.1e-123_456',
                '01234e5' => '01_234e5',
            ],
            'hexadecimal' => [
                '0x42726F776E' => '0x42_72_6F_77_6E',
                '0X42726F776E' => '0X42_72_6F_77_6E',
                '0x2726F776E' => '0x2_72_6F_77_6E',
                '0x1234567890abcdef' => '0x12_34_56_78_90_ab_cd_ef',
                '0X1234567890ABCDEF' => '0X12_34_56_78_90_AB_CD_EF',
                '0x1234e5' => '0x12_34_e5',
            ],
            'octal' => [
                '012345' => '012_345',
                '0123456' => '0123_456',
                '01234567' => '01_234_567',
                '0o123456' => '0o123_456',
            ],
        ];

        foreach ($cases as $pairsType => $pairs) {
            foreach ($pairs as $withoutSeparator => $withSeparator) {
                yield "{$pairsType}#{$withoutSeparator}" => [
                    sprintf('<?php echo %s;', $withSeparator),
                    sprintf('<?php echo %s;', $withoutSeparator),
                ];
            }
        }
    }
}
