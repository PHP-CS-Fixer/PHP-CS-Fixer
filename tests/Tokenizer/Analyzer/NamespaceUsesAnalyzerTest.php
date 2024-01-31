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
 */
final class NamespaceUsesAnalyzerTest extends TestCase
{
    /**
     * @param list<NamespaceUseAnalysis> $expected
     *
     * @dataProvider provideUsesFromTokensCases
     */
    public function testUsesFromTokens(string $code, array $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespaceUsesAnalyzer();

        self::assertSame(
            serialize($expected),
            serialize($analyzer->getDeclarationsFromTokens($tokens))
        );
    }

    public static function provideUsesFromTokensCases(): iterable
    {
        yield ['<?php // no uses', [], []];

        yield ['<?php use Foo\Bar;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Bar',
                'Bar',
                false,
                false,
                1,
                6
            ),
        ], [1]];

        yield ['<?php use Foo\Bar; use Foo\Baz;', [
            new NamespaceUseAnalysis(
                NamespaceUseAnalysis::TYPE_CLASS,
                'Foo\Bar',
                'Bar',
                false,
                false,
                1,
                6
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
        ], [1, 8]];

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
        ], [1]];

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
        ], [1]];

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
        ], [1, 12]];

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
        ], [1]];

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
        ], [1]];

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
        ], [1]];

        // TODO: How to support these:

        // Multiple imports on one line:
        // use My\Full\Classname as Another, My\Full\NSname;

        // PHP 7+ code
        // use some\namespace\{ClassA, ClassB, ClassC as C};
        // use function some\namespace\{fn_a, fn_b, fn_c};
        // use const some\namespace\{ConstA, ConstB, ConstC};
    }

    /**
     * @param NamespaceUseAnalysis[] $expected
     *
     * @dataProvider provideGetDeclarationsInNamespaceCases
     */
    public function testGetDeclarationsInNamespace(string $code, NamespaceAnalysis $namespace, array $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespaceUsesAnalyzer();

        self::assertSame(
            serialize($expected),
            serialize($analyzer->getDeclarationsInNamespace($tokens, $namespace))
        );
    }

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
