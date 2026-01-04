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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NamespaceUsesAnalyzerTest extends TestCase
{
    /**
     * @param list<NamespaceUseAnalysis> $expected
     *
     * @dataProvider provideUsesFromTokensCases
     */
    public function testUsesFromTokens(string $code, array $expected, bool $allowMulti = false): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespaceUsesAnalyzer();

        self::assertSame(
            serialize($expected),
            serialize($analyzer->getDeclarationsFromTokens($tokens, $allowMulti)),
        );
    }

    /**
     * @return iterable<array{0: string, 1: list<NamespaceUseAnalysis>, 2?: bool}>
     */
    public static function provideUsesFromTokensCases(): iterable
    {
        yield ['<?php // no uses', []];

        yield ['<?php use Foo\Bar;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Bar',
                'Bar',
                false,
                false,
                1,
                6,
            ),
        ]];

        yield ['<?php use Foo\Bar; use Foo\Baz;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Bar',
                'Bar',
                false,
                false,
                1,
                6,
            ),
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Baz',
                'Baz',
                false,
                false,
                8,
                13,
            ),
        ]];

        yield ['<?php use \Foo\Bar;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                '\Foo\Bar',
                'Bar',
                false,
                false,
                1,
                7,
            ),
        ]];

        yield ['<?php use Foo\Bar as Baz;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Bar',
                'Baz',
                true,
                false,
                1,
                10,
            ),
        ]];

        yield ['<?php use Foo\Bar as Baz; use Foo\Buz as Baz;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Bar',
                'Baz',
                true,
                false,
                1,
                10,
            ),
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Buz',
                'Baz',
                true,
                false,
                12,
                21,
            ),
        ]];

        yield ['<?php use function My\count;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_FUNCTION,
                'My\count',
                'count',
                false,
                false,
                1,
                8,
            ),
        ]];

        yield ['<?php use function My\count as myCount;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_FUNCTION,
                'My\count',
                'myCount',
                true,
                false,
                1,
                12,
            ),
        ]];

        yield ['<?php use const My\Full\CONSTANT;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CONSTANT,
                'My\Full\CONSTANT',
                'CONSTANT',
                false,
                false,
                1,
                10,
            ),
        ]];

        yield 'Comma-separated class import with multi-use parsing disabled' => ['<?php use Foo\Bar, Foo\Baz;', [], false];

        yield 'Group class import with multi-use parsing disabled' => ['<?php use Foo\{Bar, Baz};', [], false];

        yield 'Comma-separated class import' => ['<?php use Foo\Bar, Foo\Baz;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Bar',
                'Bar',
                false,
                true,
                1,
                11,
                3,
                5,
            ),
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Baz',
                'Baz',
                false,
                true,
                1,
                11,
                8,
                10,
            ),
        ], true];

        yield 'group class import' => ['<?php use Foo\{Bar, Baz};', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Bar',
                'Bar',
                false,
                true,
                1,
                11,
                6,
                6,
            ),
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Baz',
                'Baz',
                false,
                true,
                1,
                11,
                9,
                9,
            ),
        ], true];

        yield 'Comma-separated function import' => ['<?php use function Foo\bar, Foo\baz;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_FUNCTION,
                'Foo\bar',
                'bar',
                false,
                true,
                1,
                13,
                5,
                7,
            ),
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_FUNCTION,
                'Foo\baz',
                'baz',
                false,
                true,
                1,
                13,
                10,
                12,
            ),
        ], true];

        yield 'group function import' => ['<?php use function Foo\{bar, baz};', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_FUNCTION,
                'Foo\bar',
                'bar',
                false,
                true,
                1,
                13,
                8,
                8,
            ),
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_FUNCTION,
                'Foo\baz',
                'baz',
                false,
                true,
                1,
                13,
                11,
                11,
            ),
        ], true];

        yield 'Comma-separated constant import' => ['<?php use const Foo\BAR, Foo\BAZ;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CONSTANT,
                'Foo\BAR',
                'BAR',
                false,
                true,
                1,
                13,
                5,
                7,
            ),
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CONSTANT,
                'Foo\BAZ',
                'BAZ',
                false,
                true,
                1,
                13,
                10,
                12,
            ),
        ], true];

        yield 'group constant import' => ['<?php use const Foo\{BAR, BAZ};', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CONSTANT,
                'Foo\BAR',
                'BAR',
                false,
                true,
                1,
                13,
                8,
                8,
            ),
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CONSTANT,
                'Foo\BAZ',
                'BAZ',
                false,
                true,
                1,
                13,
                11,
                11,
            ),
        ], true];

        yield 'multiple multi-imports with comments' => [
            <<<'PHP'
                <?php
                use Foo\Bar, /* Foo\Baz, */ Foo\Buzz, /** Foo\Bazzz, */ Foo\Bazzzz;
                use function Bar\f1, /* Bar\f2, */ Bar\f3, /** Bar\f4, */ Bar\f5;
                use const Buzz\C1, /* Buzz\C2, */ Buzz\C3, /** Buzz\C4, */ Buzz\C5;
                PHP,
            [
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CLASS,
                    'Foo\Bar',
                    'Bar',
                    false,
                    true,
                    1,
                    20,
                    3,
                    5,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CLASS,
                    'Foo\Buzz',
                    'Buzz',
                    false,
                    true,
                    1,
                    20,
                    10,
                    12,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CLASS,
                    'Foo\Bazzzz',
                    'Bazzzz',
                    false,
                    true,
                    1,
                    20,
                    17,
                    19,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_FUNCTION,
                    'Bar\f1',
                    'f1',
                    false,
                    true,
                    22,
                    43,
                    26,
                    28,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_FUNCTION,
                    'Bar\f3',
                    'f3',
                    false,
                    true,
                    22,
                    43,
                    33,
                    35,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_FUNCTION,
                    'Bar\f5',
                    'f5',
                    false,
                    true,
                    22,
                    43,
                    40,
                    42,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CONSTANT,
                    'Buzz\C1',
                    'C1',
                    false,
                    true,
                    45,
                    66,
                    49,
                    51,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CONSTANT,
                    'Buzz\C3',
                    'C3',
                    false,
                    true,
                    45,
                    66,
                    56,
                    58,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CONSTANT,
                    'Buzz\C5',
                    'C5',
                    false,
                    true,
                    45,
                    66,
                    63,
                    65,
                ),
            ],
            true,
        ];

        yield 'multiple multi-imports with aliases' => [
            <<<'PHP'
                <?php
                use Foo\Bar, Foo\Baz as Buzz;
                use function Bar\f1, Bar\f2 as func2;
                use const Buzz\C1, Buzz\C2 as CONST2;
                PHP,
            [
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CLASS,
                    'Foo\Bar',
                    'Bar',
                    false,
                    true,
                    1,
                    15,
                    3,
                    5,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CLASS,
                    'Foo\Baz',
                    'Buzz',
                    true,
                    true,
                    1,
                    15,
                    8,
                    14,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_FUNCTION,
                    'Bar\f1',
                    'f1',
                    false,
                    true,
                    17,
                    33,
                    21,
                    23,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_FUNCTION,
                    'Bar\f2',
                    'func2',
                    true,
                    true,
                    17,
                    33,
                    26,
                    32,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CONSTANT,
                    'Buzz\C1',
                    'C1',
                    false,
                    true,
                    35,
                    51,
                    39,
                    41,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CONSTANT,
                    'Buzz\C2',
                    'CONST2',
                    true,
                    true,
                    35,
                    51,
                    44,
                    50,
                ),
            ],
            true,
        ];

        yield 'multiline grouped class import with trailing comma' => [
            <<<'PHP'
                <?php
                use Foo\{
                    Bar,
                    Baz,
                };
                PHP,
            [
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CLASS,
                    'Foo\Bar',
                    'Bar',
                    false,
                    true,
                    1,
                    14,
                    7,
                    7,
                ),
                new NamespaceUseAnalysis(
                    NamespaceUseAnalysis::TYPE_CLASS,
                    'Foo\Baz',
                    'Baz',
                    false,
                    true,
                    1,
                    14,
                    10,
                    10,
                ),
            ],
            true,
        ];
    }

    /**
     * @param list<NamespaceUseAnalysis> $expected
     *
     * @dataProvider provideGetDeclarationsInNamespaceCases
     */
    public function testGetDeclarationsInNamespace(string $code, NamespaceAnalysis $namespace, array $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespaceUsesAnalyzer();

        self::assertSame(
            serialize($expected),
            serialize($analyzer->getDeclarationsInNamespace($tokens, $namespace)),
        );
    }

    /**
     * @return iterable<int, array{0: string, 1: NamespaceAnalysis, 2: list<NamespaceUseAnalysis>}>
     */
    public static function provideGetDeclarationsInNamespaceCases(): iterable
    {
        yield [
            '<?php
                namespace Foo;
                use Bar;
                use Baz;',
            new NamespaceAnalysis('Foo', 'Foo', 2, 5, 2, 15),
            [
                new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Bar', 'Bar', false, false, 7, 10),
                new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Baz', 'Baz', false, false, 12, 15),
            ],
        ];

        yield [
            '<?php
                namespace Foo1 {
                    use Bar1;
                    use Baz1;
                }
                namespace Foo2 {
                    use Bar2;
                    use Baz2;
                }',
            new NamespaceAnalysis('Foo1', 'Foo1', 2, 4, 2, 18),
            [
                new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Bar1', 'Bar1', false, false, 8, 11),
                new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Baz1', 'Baz1', false, false, 13, 16),
            ],
        ];

        yield [
            '<?php
                namespace Foo1 {
                    use Bar1;
                    use Baz1;
                }
                namespace Foo2 {
                    use Bar2;
                    use Baz2;
                }',
            new NamespaceAnalysis('Foo2', 'Foo2', 20, 22, 20, 36),
            [
                new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Bar2', 'Bar2', false, false, 26, 29),
                new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Baz2', 'Baz2', false, false, 31, 34),
            ],
        ];
    }
}
