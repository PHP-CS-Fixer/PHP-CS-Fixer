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

use PhpCsFixer\Fixer\Basic\NumericLiteralSeparatorFixer;
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
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, ?array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'do not override existing separator' => [
            <<<'PHP'
                <?php
                echo 0B01010100_01101000;
                echo 70_10_00;

                PHP,
            null,
            [
                'override_existing' => false,
                'strategy' => NumericLiteralSeparatorFixer::STRATEGY_USE_SEPARATOR,
            ],
        ];

        yield 'override existing separator' => [
            <<<'PHP'
                <?php
                echo 1_234.5;
                echo 701_000;
                PHP,
            <<<'PHP'
                <?php
                echo 123_4.5;
                echo 70_10_00;
                PHP,
            [
                'override_existing' => true,
                'strategy' => NumericLiteralSeparatorFixer::STRATEGY_USE_SEPARATOR,
            ],
        ];

        yield from self::yieldCases([
            'decimal' => [
                '1234' => '1_234',
                '-1234' => '-1_234',
                '12345' => '12_345',
                '123456' => '123_456',
            ],
            'binary' => [
                '0b0101010001101000' => '0b01010100_01101000',
                '0b01010100011010000110010101101111' => '0b01010100_01101000_01100101_01101111',
                '0b110001000' => '0b1_10001000',
            ],
            'float' => [
                '.001' => null,
                '.1001' => '.100_1',
                '0.0001' => '0.000_1',
                '0.001' => null,
                '1234.5' => '1_234.5',
                '1.2345' => '1.234_5',
                '1234e5' => '1_234e5',
                '1234E5' => '1_234E5',
                '1e2345' => '1e2_345',
                '1234.5678e1234' => '1_234.567_8e1_234',
                '.5678e1234' => '.567_8e1_234',
                '1.1e-1234' => '1.1e-1_234',
                '1.1e-12345' => '1.1e-12_345',
                '1.1e-123456' => '1.1e-123_456',
                '.1e-12345' => '.1e-12_345',
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
            ],
        ]);

        yield 'do not change float to int when there is nothing after the dot' => ['<?php $x = 100.;'];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @requires PHP 8.1
     *
     * @dataProvider provideFix81Cases
     */
    public function testFix81(string $expected, ?string $input = null, ?array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'do not override existing separator' => [
            '<?php echo 0o123_45;',
            null,
            [
                'override_existing' => false,
                'strategy' => NumericLiteralSeparatorFixer::STRATEGY_USE_SEPARATOR,
            ],
        ];

        yield 'override existing separator' => [
            '<?php echo 1_234.5;',
            '<?php echo 123_4.5;',
            [
                'override_existing' => true,
                'strategy' => NumericLiteralSeparatorFixer::STRATEGY_USE_SEPARATOR,
            ],
        ];

        yield from self::yieldCases([
            'octal' => [
                '0o12345' => '0o12_345',
                '0o123456' => '0o123_456',
            ],
        ]);
    }

    /**
     * @param array<string, array<mixed, mixed>> $cases
     *
     * @return iterable<string, array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
    private static function yieldCases(array $cases): iterable
    {
        foreach ($cases as $pairsType => $pairs) {
            foreach ($pairs as $withoutSeparator => $withSeparator) {
                if (null === $withSeparator) {
                    yield "do not modify valid {$pairsType} {$withoutSeparator}" => [
                        sprintf('<?php echo %s;', $withoutSeparator),
                        null,
                        ['strategy' => NumericLiteralSeparatorFixer::STRATEGY_USE_SEPARATOR],
                    ];
                } else {
                    yield "add separator to {$pairsType} {$withoutSeparator}" => [
                        sprintf('<?php echo %s;', $withSeparator),
                        sprintf('<?php echo %s;', $withoutSeparator),
                        ['strategy' => NumericLiteralSeparatorFixer::STRATEGY_USE_SEPARATOR],
                    ];
                }
            }

            foreach ($pairs as $withoutSeparator => $withSeparator) {
                if (null === $withSeparator) {
                    continue;
                }

                yield "remove separator from {$pairsType} {$withoutSeparator}" => [
                    sprintf('<?php echo %s;', $withoutSeparator),
                    sprintf('<?php echo %s;', $withSeparator),
                    ['strategy' => NumericLiteralSeparatorFixer::STRATEGY_NO_SEPARATOR],
                ];
            }
        }
    }
}
