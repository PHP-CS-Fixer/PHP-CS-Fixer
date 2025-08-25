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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DataProviderAnalyzerTest extends TestCase
{
    /**
     * @param list<DataProviderAnalysis> $expected
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
     * @return iterable<string, array{list<DataProviderAnalysis>, string}>
     */
    public static function provideGettingDataProvidersCases(): iterable
    {
        yield 'single data provider' => [
            [new DataProviderAnalysis('provider', 28, [[11, 23]])],
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider
                 */
                public function testFoo() {}
                public function provider() {}
            }',
        ];

        yield 'single data provider with different casing' => [
            [new DataProviderAnalysis('dataProvider', 28, [[11, 23]])],
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider dataPROVIDER
                 */
                public function testFoo() {}
                public function dataProvider() {}
            }',
        ];

        yield 'single static data provider' => [
            [new DataProviderAnalysis('provider', 30, [[11, 23]])],
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
                new DataProviderAnalysis('provider1', 28, [[11, 23]]),
                new DataProviderAnalysis('provider2', 39, [[11, 66]]),
                new DataProviderAnalysis('provider3', 50, [[11, 109]]),
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

        yield 'single data provider with multiple usage' => [
            [
                new DataProviderAnalysis('provider', 28, [[11, 23], [35, 23]]),
            ],
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider
                 */
                public function testFoo() {}
                public function provider() {}
                /**
                 * @dataProvider provider
                 */
                public function testFoo2() {}
            }',
        ];

        foreach (['abstract', 'final', 'private', 'protected', 'static', '/* private */'] as $modifier) {
            yield \sprintf('test function with %s modifier', $modifier) => [
                [
                    new DataProviderAnalysis('provider1', 54, [[37, 4]]),
                    new DataProviderAnalysis('provider2', 65, [[11, 4]]),
                    new DataProviderAnalysis('provider3', 76, [[24, 4]]),
                ],
                \sprintf('<?php class FooTest extends TestCase {
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
                new DataProviderAnalysis('provider2', 93, [[65, 27]]),
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

    /**
     * @param list<DataProviderAnalysis> $expected
     *
     * @requires PHP ^8.0
     *
     * @dataProvider provideGettingDataProviders80Cases
     */
    public function testGettingDataProviders80(array $expected, string $code, int $startIndex = 0, ?int $endIndex = null): void
    {
        $this->testGettingDataProviders($expected, $code, $startIndex, $endIndex);
    }

    /**
     * @return iterable<string, array{list<DataProviderAnalysis>, string}>
     */
    public static function provideGettingDataProviders80Cases(): iterable
    {
        yield 'with an attribute between PHPDoc and test method' => [
            [new DataProviderAnalysis('provideFooCases', 35, [[11, 11]])],
            <<<'PHP'
                <?php
                class FooTest extends TestCase {
                    /**
                     * @dataProvider provideFooCases
                     */
                    #[CustomAttribute]
                    public function testFoo(): void {}
                    public function provideFooCases(): iterable {}
                }
                PHP,
        ];

        yield 'with multiple DataProvider attributes' => [
            [
                new DataProviderAnalysis('provider1', 70, [[21, 0]]),
                new DataProviderAnalysis('provider2', 84, [[35, 0]]),
                new DataProviderAnalysis('provider3', 98, [[48, 0]]),
            ],
            <<<'PHP'
                <?php
                class FooTest extends TestCase {
                    #[\PHPUnit\Framework\Attributes\DataProvider('provider1')]
                    #[\PHPUnit\Framework\Attributes\DataProvider('provider2')]
                    #[PHPUnit\Framework\Attributes\DataProvider('provider3')]
                    public function testFoo(): void {}
                    public function provider1(): iterable {}
                    public function provider2(): iterable {}
                    public function provider3(): iterable {}
                }
                PHP,
        ];

        yield 'with incorrect DataProvider attributes' => [
            [],
            <<<'PHP'
                <?php
                namespace NamespaceToMakeAttributeWithoutLeadingSlashIgnored;
                class FooTest extends TestCase {
                    #[PHPUnit\Framework\Attributes\DataProvider('provider1')]
                    #[\PHPUnit\Framework\Attributes\DataProvider]
                    #[\PHPUnit\Framework\Attributes\DataProvider(123)]
                    #[\PHPUnit\Framework\Attributes\DataProvider('doNotGetFooledByConcatenation' . 'provider3')]
                    public function testFoo(): void {}
                    public function provider1(): iterable {}
                    public function provider2(): iterable {}
                    public function provider3(): iterable {}
                }
                PHP,
        ];

        yield 'with DataProvider attributes use in a tricky way' => [
            [
                new DataProviderAnalysis('provider1', 151, [[60, 0], [126, 0]]),
                new DataProviderAnalysis('provider2', 165, [[84, 0]]),
                new DataProviderAnalysis('provider3', 179, [[95, 0]]),
            ],
            <<<'PHP'
                <?php
                namespace N;
                use PHPUnit\Framework as PphUnitAlias;
                use PHPUnit\Framework\Attributes;
                class FooTest extends TestCase {
                    #[
                        \PHPUnit\Framework\Attributes\BackupGlobals(true),
                        \PHPUnit\Framework\Attributes\DataProvider('provider1'),
                        \PHPUnit\Framework\Attributes\Group('foo'),
                    ]
                    #[Attributes\DataProvider('provider2')]
                    #[PphUnitAlias\Attributes\DataProvider('provider3')]
                    public function testFoo(int $x): void {}
                    #[\PHPUnit\Framework\Attributes\DataProvider('provider1')]
                    public function testBar(int $x): void {}
                    public function provider1(): iterable {}
                    public function provider2(): iterable {}
                    public function provider3(): iterable {}
                }
                PHP,
        ];
    }
}
