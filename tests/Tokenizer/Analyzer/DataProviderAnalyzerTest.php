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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\DataProviderAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\DataProviderAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\DataProviderAnalyzer
 */
final class DataProviderAnalyzerTest extends TestCase
{
    /**
     * @param array<int> $expected
     *
     * @dataProvider provideGettingDataProvidersCases
     */
    public function testGettingDataProviders(array $expected, string $code, int $startIndex = 0, ?int $endIndex = null): void
    {
        $tokens = Tokens::fromCode($code);
        if (null === $endIndex) {
            $endIndex = $tokens->count() - 1;
        }
        $analyzer = new DataProviderAnalyzer();

        self::assertSame(serialize($expected), serialize($analyzer->getDataProviders($tokens, $startIndex, $endIndex)));
    }

    /**
     * @return iterable<array{array<DataProviderAnalysis>, string}>
     */
    public static function provideGettingDataProvidersCases(): iterable
    {
        yield 'single data provider' => [
            [new DataProviderAnalysis('provider', 28, [11])],
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider
                 */
                public function testFoo() {}
                public function provider() {}
            }',
        ];

        yield 'single data provider with different casing' => [
            [new DataProviderAnalysis('dataProvider', 28, [11])],
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider dataPROVIDER
                 */
                public function testFoo() {}
                public function dataProvider() {}
            }',
        ];

        yield 'single static data provider' => [
            [new DataProviderAnalysis('provider', 30, [11])],
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider
                 */
                public function testFoo() {}
                public static function provider() {}
            }',
        ];

        yield 'multiple data provider' => [
            [
                new DataProviderAnalysis('provider1', 28, [11]),
                new DataProviderAnalysis('provider2', 39, [11]),
                new DataProviderAnalysis('provider3', 50, [11]),
            ],
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider1
                 * @dataProvider provider2
                 * @dataProvider provider3
                 */
                public function testFoo() {}
                public function provider1() {}
                public function provider2() {}
                public function provider3() {}
            }',
        ];

        foreach (['abstract', 'final', 'private', 'protected', 'static', '/* private */'] as $modifier) {
            yield sprintf('test function with %s modifier', $modifier) => [
                [
                    new DataProviderAnalysis('provider1', 54, [37]),
                    new DataProviderAnalysis('provider2', 65, [11]),
                    new DataProviderAnalysis('provider3', 76, [24]),
                ],
                sprintf('<?php class FooTest extends TestCase {
                    /** @dataProvider provider2 */
                    public function testFoo1() {}
                    /** @dataProvider provider3 */
                    %s function testFoo2() {}
                    /** @dataProvider provider1 */
                    public function testFoo3() {}
                    public function provider1() {}
                    public function provider2() {}
                    public function provider3() {}
                }', $modifier),
            ];
        }

        yield 'not existing data provider used' => [
            [],
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider
                 */
                public function testFoo() {}
            }',
        ];

        yield 'data provider being constant' => [
            [],
            '<?php class FooTest extends TestCase {
                private const provider = [];
                /**
                 * @dataProvider provider
                 */
                public function testFoo() {}
            }',
        ];

        yield 'ignore anonymous function' => [
            [
                new DataProviderAnalysis('provider2', 93, [65]),
            ],
            '<?php class FooTest extends TestCase {
                public function testFoo0() {}
                /**
                 * @dataProvider provider0
                 */
                public function testFoo1()
                {
                    /**
                     * @dataProvider provider1
                     */
                     $f = function ($x, $y) { return $x + $y; };
                }
                    /**
                     * @dataProvider provider2
                     */
                public function testFoo2() {}
                public function provider1() {}
                public function provider2() {}
            }',
        ];
    }
}
