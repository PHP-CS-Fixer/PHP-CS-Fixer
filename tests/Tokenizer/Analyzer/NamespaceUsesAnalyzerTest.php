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
     * @dataProvider provideNamespaceUsesCases
     */
    public function testUsesFromTokens(string $code, array $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespaceUsesAnalyzer();

        static::assertSame(
            serialize($expected),
            serialize($analyzer->getDeclarationsFromTokens($tokens))
        );
    }

    public static function provideNamespaceUsesCases(): array
    {
        return [
            ['<?php // no uses', [], []],
            ['<?php use Foo\Bar;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Bar',
                    false,
                    1,
                    6,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], [1]],
            ['<?php use Foo\Bar; use Foo\Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Bar',
                    false,
                    1,
                    6,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
                new NamespaceUseAnalysis(
                    'Foo\Baz',
                    'Baz',
                    false,
                    8,
                    13,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], [1, 8]],
            ['<?php use \Foo\Bar;', [
                new NamespaceUseAnalysis(
                    '\Foo\Bar',
                    'Bar',
                    false,
                    1,
                    7,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], [1]],
            ['<?php use Foo\Bar as Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Baz',
                    true,
                    1,
                    10,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], [1]],
            ['<?php use Foo\Bar as Baz; use Foo\Buz as Baz;', [
                new NamespaceUseAnalysis(
                    'Foo\Bar',
                    'Baz',
                    true,
                    1,
                    10,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
                new NamespaceUseAnalysis(
                    'Foo\Buz',
                    'Baz',
                    true,
                    12,
                    21,
                    NamespaceUseAnalysis::TYPE_CLASS
                ),
            ], [1, 12]],
            ['<?php use function My\count;', [
                new NamespaceUseAnalysis(
                    'My\count',
                    'count',
                    false,
                    1,
                    8,
                    NamespaceUseAnalysis::TYPE_FUNCTION
                ),
            ], [1]],
            ['<?php use function My\count as myCount;', [
                new NamespaceUseAnalysis(
                    'My\count',
                    'myCount',
                    true,
                    1,
                    12,
                    NamespaceUseAnalysis::TYPE_FUNCTION
                ),
            ], [1]],
            ['<?php use const My\Full\CONSTANT;', [
                new NamespaceUseAnalysis(
                    'My\Full\CONSTANT',
                    'CONSTANT',
                    false,
                    1,
                    10,
                    NamespaceUseAnalysis::TYPE_CONSTANT
                ),
            ], [1]],

            // TODO: How to support these:

            // Multiple imports on one line:
            // use My\Full\Classname as Another, My\Full\NSname;

            // PHP 7+ code
            // use some\namespace\{ClassA, ClassB, ClassC as C};
            // use function some\namespace\{fn_a, fn_b, fn_c};
            // use const some\namespace\{ConstA, ConstB, ConstC};
        ];
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

        static::assertSame(
            serialize($expected),
            serialize($analyzer->getDeclarationsInNamespace($tokens, $namespace))
        );
    }

    public static function provideGetDeclarationsInNamespaceCases(): array
    {
        return [
            [
                '<?php
                namespace Foo;
                use Bar;
                use Baz;',
                new NamespaceAnalysis('Foo', 'Foo', 2, 5, 2, 15),
                [
                    new NamespaceUseAnalysis('Bar', 'Bar', false, 7, 10, NamespaceUseAnalysis::TYPE_CLASS),
                    new NamespaceUseAnalysis('Baz', 'Baz', false, 12, 15, NamespaceUseAnalysis::TYPE_CLASS),
                ],
            ],
            [
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
                    new NamespaceUseAnalysis('Bar1', 'Bar1', false, 8, 11, NamespaceUseAnalysis::TYPE_CLASS),
                    new NamespaceUseAnalysis('Baz1', 'Baz1', false, 13, 16, NamespaceUseAnalysis::TYPE_CLASS),
                ],
            ],
            [
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
                    new NamespaceUseAnalysis('Bar2', 'Bar2', false, 26, 29, NamespaceUseAnalysis::TYPE_CLASS),
                    new NamespaceUseAnalysis('Baz2', 'Baz2', false, 31, 34, NamespaceUseAnalysis::TYPE_CLASS),
                ],
            ],
        ];
    }
}
