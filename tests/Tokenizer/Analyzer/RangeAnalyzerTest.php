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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\RangeAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\RangeAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RangeAnalyzerTest extends TestCase
{
    /**
     * @param array{start: int, end: int} $range1
     * @param array{start: int, end: int} $range2
     *
     * @dataProvider provideRangeEqualsRangeCases
     */
    public function testRangeEqualsRange(bool $expected, string $code, array $range1, array $range2): void
    {
        $tokens = Tokens::fromCode($code);

        self::assertSame($expected, RangeAnalyzer::rangeEqualsRange($tokens, $range1, $range2));
    }

    /**
     * @return iterable<array{bool, string, array{start: int, end: int}, array{start: int, end: int}}>
     */
    public static function provideRangeEqualsRangeCases(): iterable
    {
        $ranges = [
            [['start' => 2, 'end' => 6], ['start' => 10, 'end' => 14]],
            [['start' => 10, 'end' => 14], ['start' => 20, 'end' => 24]],
            [['start' => 1, 'end' => 6], ['start' => 20, 'end' => 25]],
        ];

        foreach ($ranges as $i => [$range1, $range2]) {
            yield 'extra "()" and space #'.$i => [
                true,
                '<?php
                    $a = 1;
                    ($a = 1);
                    (($a = 1 ));
                ',
                $range1,
                $range2,
            ];
        }

        yield [
            false,
            '<?php echo 1;',
            ['start' => 0, 'end' => 1],
            ['start' => 1, 'end' => 2],
        ];

        yield 'comment + space' => [
            true,
            '<?php
                foo(1);
                /* */ foo/* */(1) /* */ ;
            ',
            ['start' => 1, 'end' => 5],
            ['start' => 9, 'end' => 17],
        ];
    }

    /**
     * @requires PHP <8.0
     */
    public function testFixPrePHP8x0(): void
    {
        $code = '<?php
            $a = [1,2,3];
            echo $a[1];
            echo $a{1};
        ';

        $tokens = Tokens::fromCode($code);

        $ranges = [
            [
                ['start' => 15, 'end' => 21],
                ['start' => 23, 'end' => 29],
            ],
            [
                ['start' => 17, 'end' => 20],
                ['start' => 24, 'end' => 28],
            ],
        ];

        foreach ($ranges as [$range1, $range2]) {
            self::assertTrue(RangeAnalyzer::rangeEqualsRange($tokens, $range1, $range2));
        }
    }
}
