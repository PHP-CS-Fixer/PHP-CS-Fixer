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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @requires PHP 8.0
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\NameQualifiedTransformer
 */
final class NameQualifiedTransformerTest extends AbstractTransformerTestCase
{
    use AssertTokensTrait;

    /**
     * @param Token[]      $expected
     * @param null|Token[] $input
     *
     * @dataProvider provideProcessCases
     *
     * @requires PHP 8.0
     */
    public function testProcess(array $expected, array $input = null): void
    {
        $expectedTokens = Tokens::fromArray($expected);
        $tokens = null === $input
            ? Tokens::fromArray($expected)
            : Tokens::fromArray($input);

        $tokenCount = \count($tokens);

        for ($i = 0; $i < $tokenCount; ++$i) {
            $this->transformer->process($tokens, $tokens[$i], $i);
        }

        self::assertTokens($expectedTokens, $tokens);

        if (null === $input) {
            self::assertFalse($tokens->isChanged());
        } else {
            self::testProcess($expected);
            self::assertTrue($tokens->isChanged());
        }
    }

    public static function provideProcessCases(): iterable
    {
        yield 'string' => [
            [
                new Token([T_OPEN_TAG, "<?php\n"]),
                new Token([T_STRING, 'Foo']),
                new Token(';'),
            ],
        ];

        if (\defined('T_NAME_RELATIVE')) { // @TODO: drop condition when PHP 8.0+ is required
            yield 'relative 1' => [
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NAMESPACE, 'namespace']),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Transformer']),
                    new Token(';'),
                ],
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NAME_RELATIVE, 'namespace\Transformer']),
                    new Token(';'),
                ],
            ];

            yield 'relative 2' => [
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NAMESPACE, 'namespace']),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Transformer']),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Foo']),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Bar']),
                    new Token(';'),
                ],
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NAME_RELATIVE, 'namespace\Transformer\Foo\Bar']),
                    new Token(';'),
                ],
            ];
        }

        if (\defined('T_NAME_FULLY_QUALIFIED')) { // @TODO: drop condition when PHP 8.0+ is required
            yield 'name fully qualified 1' => [
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Foo']),
                    new Token(';'),
                ],
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NAME_FULLY_QUALIFIED, '\Foo']),
                    new Token(';'),
                ],
            ];

            yield 'name qualified 1' => [
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_STRING, 'Foo']),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Bar']),
                    new Token(';'),
                ],
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NAME_QUALIFIED, 'Foo\Bar']),
                    new Token(';'),
                ],
            ];

            yield 'name qualified 2' => [
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Foo']),
                    new Token([T_NS_SEPARATOR, '\\']),
                    new Token([T_STRING, 'Bar']),
                    new Token(';'),
                ],
                [
                    new Token([T_OPEN_TAG, "<?php\n"]),
                    new Token([T_NAME_QUALIFIED, '\Foo\Bar']),
                    new Token(';'),
                ],
            ];
        }
    }

    /**
     * @param Token[] $expected
     *
     * @dataProvider providePriorityCases
     */
    public function testPriority(array $expected, string $source): void
    {
        self::assertTokens(Tokens::fromArray($expected), Tokens::fromCode($source));
    }

    public static function providePriorityCases(): iterable
    {
        yield [
            [
                new Token([T_OPEN_TAG, '<?php ']),
                new Token([T_STRING, 'Foo']),
                new Token(';'),
                new Token([T_STRING, 'Bar']),
                new Token(';'),
            ],
            '<?php Foo;Bar;',
        ];

        yield [
            [
                new Token([T_OPEN_TAG, '<?php ']),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar1']),
                new Token(';'),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar2']),
                new Token(';'),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar3']),
                new Token(';'),
            ],
            '<?php Foo\Bar1;\Foo\Bar2;Foo\Bar3;',
        ];

        yield [
            [
                new Token([T_OPEN_TAG, '<?php ']),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar1']),
                new Token(';'),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar2']),
                new Token(';'),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar3']),
                new Token(';'),
            ],
            '<?php Foo\Bar1;Foo\Bar2;Foo\Bar3;',
        ];

        yield [
            [
                new Token([T_OPEN_TAG, '<?php ']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar1']),
                new Token(';'),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar2']),
                new Token(';'),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Foo']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar3']),
                new Token(';'),
            ],
            '<?php \Foo\Bar1;\Foo\Bar2;\Foo\Bar3;',
        ];

        yield [
            [
                new Token([T_OPEN_TAG, '<?php ']),
                new Token([CT::T_NAMESPACE_OPERATOR, 'namespace']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Transformer']),
                new Token(';'),
            ],
            '<?php namespace\\Transformer;',
        ];

        yield [
            [
                new Token([T_OPEN_TAG, '<?php ']),
                new Token([T_NAMESPACE, 'namespace']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Foo']),
                new Token(';'),
                new Token([T_NAMESPACE, 'namespace']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Bar']),
                new Token(';'),
            ],
            '<?php namespace\Foo;namespace\Bar;',
        ];
    }
}
