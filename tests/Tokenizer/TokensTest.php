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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Tokens
 */
final class TokensTest extends TestCase
{
    use AssertTokensTrait;

    public function testReadFromCacheAfterClearing(): void
    {
        $code = '<?php echo 1;';
        $tokens = Tokens::fromCode($code);

        $countBefore = $tokens->count();

        for ($i = 0; $i < $countBefore; ++$i) {
            $tokens->clearAt($i);
        }

        $tokens = Tokens::fromCode($code);

        static::assertSame($countBefore, $tokens->count());
    }

    /**
     * @param Token[]         $sequence
     * @param null|array|bool $caseSensitive
     *
     * @dataProvider provideFindSequenceCases
     */
    public function testFindSequence(
        string $source,
        array $expected = null,
        array $sequence,
        int $start = null,
        ?int $end = null,
        $caseSensitive = null
    ): void {
        if (null === $start) {
            $start = 0;
        }
        if (null === $caseSensitive) {
            $caseSensitive = true;
        }

        $tokens = Tokens::fromCode($source);

        static::assertEqualsTokensArray(
            $expected,
            $tokens->findSequence(
                $sequence,
                $start,
                $end,
                $caseSensitive
            )
        );
    }

    public function provideFindSequenceCases(): array
    {
        return [
            [
                '<?php $x = 1;',
                null,
                [
                    new Token(';'),
                ],
                7,
            ],
            [
                '<?php $x = 2;',
                null,
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$y'],
                ],
            ],
            [
                '<?php $x = 3;',
                [
                    0 => new Token([T_OPEN_TAG, '<?php ']),
                    1 => new Token([T_VARIABLE, '$x']),
                ],
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$x'],
                ],
            ],
            [
                '<?php $x = 4;',
                [
                    3 => new Token('='),
                    5 => new Token([T_LNUMBER, '4']),
                    6 => new Token(';'),
                ],
                [
                    '=',
                    [T_LNUMBER, '4'],
                    ';',
                ],
            ],
            [
                '<?php $x = 5;',
                [
                    0 => new Token([T_OPEN_TAG, '<?php ']),
                    1 => new Token([T_VARIABLE, '$x']),
                ],
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$x'],
                ],
                0,
            ],
            [
                '<?php $x = 6;',
                null,
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$x'],
                ],
                1,
            ],
            [
                '<?php $x = 7;',
                [
                    3 => new Token('='),
                    5 => new Token([T_LNUMBER, '7']),
                    6 => new Token(';'),
                ],
                [
                    '=',
                    [T_LNUMBER, '7'],
                    ';',
                ],
                3,
                6,
            ],
            [
                '<?php $x = 8;',
                null,
                [
                    '=',
                    [T_LNUMBER, '8'],
                    ';',
                ],
                4,
                6,
            ],
            [
                '<?php $x = 9;',
                null,
                [
                    '=',
                    [T_LNUMBER, '9'],
                    ';',
                ],
                3,
                5,
            ],
            [
                '<?php $x = 10;',
                [
                    0 => new Token([T_OPEN_TAG, '<?php ']),
                    1 => new Token([T_VARIABLE, '$x']),
                ],
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$x'],
                ],
                0,
                1,
                true,
            ],
            [
                '<?php $x = 11;',
                null,
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$X'],
                ],
                0,
                1,
                true,
            ],
            [
                '<?php $x = 12;',
                null,
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$X'],
                ],
                0,
                1,
                [1 => true],
            ],
            [
                '<?php $x = 13;',
                [
                    0 => new Token([T_OPEN_TAG, '<?php ']),
                    1 => new Token([T_VARIABLE, '$x']),
                ],
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$X'],
                ],
                0,
                1,
                false,
            ],
            [
                '<?php $x = 14;',
                [
                    0 => new Token([T_OPEN_TAG, '<?php ']),
                    1 => new Token([T_VARIABLE, '$x']),
                ],
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$X'],
                ],
                0,
                1,
                [1 => false],
            ],
            [
                '<?php $x = 15;',
                [
                    0 => new Token([T_OPEN_TAG, '<?php ']),
                    1 => new Token([T_VARIABLE, '$x']),
                ],
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$X'],
                ],
                0,
                1,
                [1 => false],
            ],
            [
                '<?php $x = 16;',
                null,
                [
                    [T_OPEN_TAG],
                    [T_VARIABLE, '$X'],
                ],
                0,
                1,
                [2 => false],
            ],
            [
                '<?php $x = 17;',
                null,
                [
                    [T_VARIABLE, '$X'],
                    '=',
                ],
                0,
                10,
            ],
        ];
    }

    /**
     * @dataProvider provideFindSequenceExceptionCases
     */
    public function testFindSequenceException(string $message, array $sequence): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $tokens = Tokens::fromCode('<?php $x = 1;');
        $tokens->findSequence($sequence);
    }

    public function provideFindSequenceExceptionCases(): array
    {
        $emptyToken = new Token('');

        return [
            ['Invalid sequence.', []],
            [
                'Non-meaningful token at position: "0".',
                [[T_WHITESPACE, '   ']],
            ],
            [
                'Non-meaningful token at position: "1".',
                ['{', [T_COMMENT, '// Foo'], '}'],
            ],
            [
                'Non-meaningful (empty) token at position: "2".',
                ['{', '!', $emptyToken, '}'],
            ],
        ];
    }

    public function testClearRange(): void
    {
        $source = <<<'PHP'
<?php
class FooBar
{
    public function foo()
    {
        return 'bar';
    }

    public function bar()
    {
        return 'foo';
    }
}
PHP;

        $tokens = Tokens::fromCode($source);
        [$fooIndex, $barIndex] = array_keys($tokens->findGivenKind(T_PUBLIC));

        $tokens->clearRange($fooIndex, $barIndex - 1);

        $newPublicIndexes = array_keys($tokens->findGivenKind(T_PUBLIC));
        static::assertSame($barIndex, reset($newPublicIndexes));

        for ($i = $fooIndex; $i < $barIndex; ++$i) {
            static::assertTrue($tokens[$i]->isWhitespace());
        }
    }

    /**
     * @dataProvider provideMonolithicPhpDetectionCases
     */
    public function testMonolithicPhpDetection(string $source, bool $isMonolithic): void
    {
        $tokens = Tokens::fromCode($source);
        static::assertSame($isMonolithic, $tokens->isMonolithicPhp());
    }

    public function provideMonolithicPhpDetectionCases(): array
    {
        return [
            ["<?php\n", true],
            ["<?php\n?>", true],
            ['', false],
            [' ', false],
            ["#!/usr/bin/env php\n<?php\n", false],
            [" <?php\n", false],
            ["<?php\n?> ", false],
            ["<?php\n?><?php\n", false],
        ];
    }

    /**
     * @dataProvider provideShortOpenTagMonolithicPhpDetectionCases
     */
    public function testShortOpenTagMonolithicPhpDetection(string $source, bool $monolithic): void
    {
        if (!ini_get('short_open_tag')) {
            $monolithic = false;
        }

        $tokens = Tokens::fromCode($source);
        static::assertSame($monolithic, $tokens->isMonolithicPhp());
    }

    public function provideShortOpenTagMonolithicPhpDetectionCases(): array
    {
        return [
            ["<?\n", true],
            ["<?\n?>", true],
            [" <?\n", false],
            ["<?\n?> ", false],
            ["<?\n?><?\n", false],
            ["<?\n?><?php\n", false],
            ["<?\n?><?=' ';\n", false],
            ["<?php\n?><?\n", false],
            ["<?=' '\n?><?\n", false],
        ];
    }

    /**
     * @dataProvider provideShortOpenTagEchoMonolithicPhpDetectionCases
     */
    public function testShortOpenTagEchoMonolithicPhpDetection(string $source, bool $monolithic): void
    {
        $tokens = Tokens::fromCode($source);
        static::assertSame($monolithic, $tokens->isMonolithicPhp());
    }

    public function provideShortOpenTagEchoMonolithicPhpDetectionCases(): array
    {
        return [
            ["<?=' ';\n", true],
            ["<?=' '?>", true],
            [" <?=' ';\n", false],
            ["<?=' '?> ", false],
            ["<?php\n?><?=' ';\n", false],
            ["<?=' '\n?><?php\n", false],
            ["<?=' '\n?><?=' ';\n", false],
        ];
    }

    public function testTokenKindsFound(): void
    {
        $code = <<<'EOF'
<?php

class Foo
{
    public $foo;
}

if (!function_exists('bar')) {
    function bar()
    {
        return 'bar';
    }
}
EOF;

        $tokens = Tokens::fromCode($code);

        static::assertTrue($tokens->isTokenKindFound(T_CLASS));
        static::assertTrue($tokens->isTokenKindFound(T_RETURN));
        static::assertFalse($tokens->isTokenKindFound(T_INTERFACE));
        static::assertFalse($tokens->isTokenKindFound(T_ARRAY));

        static::assertTrue($tokens->isAllTokenKindsFound([T_CLASS, T_RETURN]));
        static::assertFalse($tokens->isAllTokenKindsFound([T_CLASS, T_INTERFACE]));

        static::assertTrue($tokens->isAnyTokenKindsFound([T_CLASS, T_RETURN]));
        static::assertTrue($tokens->isAnyTokenKindsFound([T_CLASS, T_INTERFACE]));
        static::assertFalse($tokens->isAnyTokenKindsFound([T_INTERFACE, T_ARRAY]));
    }

    public function testFindGivenKind(): void
    {
        $source = <<<'PHP'
<?php
class FooBar
{
    public function foo()
    {
        return 'bar';
    }

    public function bar()
    {
        return 'foo';
    }
}
PHP;
        $tokens = Tokens::fromCode($source);
        /** @var Token[] $found */
        $found = $tokens->findGivenKind(T_CLASS);
        static::assertIsArray($found);
        static::assertCount(1, $found);
        static::assertArrayHasKey(1, $found);
        static::assertSame(T_CLASS, $found[1]->getId());

        $found = $tokens->findGivenKind([T_CLASS, T_FUNCTION]);
        static::assertCount(2, $found);
        static::assertArrayHasKey(T_CLASS, $found);
        static::assertIsArray($found[T_CLASS]);
        static::assertCount(1, $found[T_CLASS]);
        static::assertArrayHasKey(1, $found[T_CLASS]);
        static::assertSame(T_CLASS, $found[T_CLASS][1]->getId());

        static::assertArrayHasKey(T_FUNCTION, $found);
        static::assertIsArray($found[T_FUNCTION]);
        static::assertCount(2, $found[T_FUNCTION]);
        static::assertArrayHasKey(9, $found[T_FUNCTION]);
        static::assertSame(T_FUNCTION, $found[T_FUNCTION][9]->getId());
        static::assertArrayHasKey(26, $found[T_FUNCTION]);
        static::assertSame(T_FUNCTION, $found[T_FUNCTION][26]->getId());

        // test offset and limits of the search
        $found = $tokens->findGivenKind([T_CLASS, T_FUNCTION], 10);
        static::assertCount(0, $found[T_CLASS]);
        static::assertCount(1, $found[T_FUNCTION]);
        static::assertArrayHasKey(26, $found[T_FUNCTION]);

        $found = $tokens->findGivenKind([T_CLASS, T_FUNCTION], 2, 10);
        static::assertCount(0, $found[T_CLASS]);
        static::assertCount(1, $found[T_FUNCTION]);
        static::assertArrayHasKey(9, $found[T_FUNCTION]);
    }

    /**
     * @param Token[] $expected tokens
     * @param int[]   $indexes  to clear
     *
     * @dataProvider provideGetClearTokenAndMergeSurroundingWhitespaceCases
     */
    public function testClearTokenAndMergeSurroundingWhitespace(string $source, array $indexes, array $expected): void
    {
        $this->doTestClearTokens($source, $indexes, $expected);
        if (\count($indexes) > 1) {
            $this->doTestClearTokens($source, array_reverse($indexes), $expected);
        }
    }

    public function provideGetClearTokenAndMergeSurroundingWhitespaceCases(): array
    {
        $clearToken = new Token('');

        return [
            [
                '<?php if($a){}else{}',
                [7, 8, 9],
                [
                    new Token([T_OPEN_TAG, '<?php ']),
                    new Token([T_IF, 'if']),
                    new Token('('),
                    new Token([T_VARIABLE, '$a']),
                    new Token(')'),
                    new Token('{'),
                    new Token('}'),
                    $clearToken,
                    $clearToken,
                    $clearToken,
                ],
            ],
            [
                '<?php $a;/**/;',
                [2],
                [
                    // <?php $a /**/;
                    new Token([T_OPEN_TAG, '<?php ']),
                    new Token([T_VARIABLE, '$a']),
                    $clearToken,
                    new Token([T_COMMENT, '/**/']),
                    new Token(';'),
                ],
            ],
            [
                '<?php ; ; ;',
                [3],
                [
                    // <?php ;  ;
                    new Token([T_OPEN_TAG, '<?php ']),
                    new Token(';'),
                    new Token([T_WHITESPACE, '  ']),
                    $clearToken,
                    $clearToken,
                    new Token(';'),
                ],
            ],
            [
                '<?php ; ; ;',
                [1, 5],
                [
                    // <?php  ;
                    new Token([T_OPEN_TAG, '<?php ']),
                    new Token([T_WHITESPACE, ' ']),
                    $clearToken,
                    new Token(';'),
                    new Token([T_WHITESPACE, ' ']),
                    $clearToken,
                ],
            ],
            [
                '<?php ; ; ;',
                [1, 3],
                [
                    // <?php   ;
                    new Token([T_OPEN_TAG, '<?php ']),
                    new Token([T_WHITESPACE, '  ']),
                    $clearToken,
                    $clearToken,
                    $clearToken,
                    new Token(';'),
                ],
            ],
            [
                '<?php ; ; ;',
                [1],
                [
                    // <?php  ; ;
                    new Token([T_OPEN_TAG, '<?php ']),
                    new Token([T_WHITESPACE, ' ']),
                    $clearToken,
                    new Token(';'),
                    new Token([T_WHITESPACE, ' ']),
                    new Token(';'),
                ],
            ],
        ];
    }

    /**
     * @param ?int $expectedIndex
     *
     * @dataProvider provideTokenOfKindSiblingCases
     */
    public function testTokenOfKindSibling(
        ?int $expectedIndex,
        int $direction,
        int $index,
        array $findTokens,
        bool $caseSensitive = true
    ): void {
        $source =
            '<?php
                $a = function ($b) {
                    return $b;
                };

                echo $a(1);
                // test
                return 123;';

        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);
        if (1 === $direction) {
            static::assertSame($expectedIndex, $tokens->getNextTokenOfKind($index, $findTokens, $caseSensitive));
        } else {
            static::assertSame($expectedIndex, $tokens->getPrevTokenOfKind($index, $findTokens, $caseSensitive));
        }

        static::assertSame($expectedIndex, $tokens->getTokenOfKindSibling($index, $direction, $findTokens, $caseSensitive));
    }

    public function provideTokenOfKindSiblingCases(): array
    {
        return [
            // find next cases
            [
                35, 1, 34, [';'],
            ],
            [
                14, 1, 0, [[T_RETURN]],
            ],
            [
                32, 1, 14, [[T_RETURN]],
            ],
            [
                6, 1, 0, [[T_RETURN], [T_FUNCTION]],
            ],
            // find previous cases
            [
                14, -1, 32, [[T_RETURN], [T_FUNCTION]],
            ],
            [
                6, -1, 7, [[T_FUNCTION]],
            ],
            [
                null, -1, 6, [[T_FUNCTION]],
            ],
        ];
    }

    /**
     * @dataProvider provideFindBlockEndCases
     */
    public function testFindBlockEnd(int $expectedIndex, string $source, int $type, int $searchIndex): void
    {
        static::assertFindBlockEnd($expectedIndex, $source, $type, $searchIndex);
    }

    public function provideFindBlockEndCases(): array
    {
        return [
            [4, '<?php ${$bar};', Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE, 2],
            [4, '<?php test(1);', Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, 2],
            [4, '<?php $a{1};', Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE, 2],
            [4, '<?php $a[1];', Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, 2],
            [6, '<?php [1, "foo"];', Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, 1],
            [5, '<?php $foo->{$bar};', Tokens::BLOCK_TYPE_DYNAMIC_PROP_BRACE, 3],
            [4, '<?php list($a) = $b;', Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, 2],
            [6, '<?php if($a){}?>', Tokens::BLOCK_TYPE_CURLY_BRACE, 5],
            [11, '<?php $foo = (new Foo());', Tokens::BLOCK_TYPE_BRACE_CLASS_INSTANTIATION, 5],
            [10, '<?php $object->{"set_{$name}"}(42);', Tokens::BLOCK_TYPE_DYNAMIC_PROP_BRACE, 3],
            [19, '<?php $foo = (new class () implements Foo {});', Tokens::BLOCK_TYPE_BRACE_CLASS_INSTANTIATION, 5],
        ];
    }

    /**
     * @requires PHP 7.1
     * @dataProvider provideFindBlockEnd71Cases
     */
    public function testFindBlockEnd71(int $expectedIndex, string $source, int $type, int $searchIndex): void
    {
        static::assertFindBlockEnd($expectedIndex, $source, $type, $searchIndex);
    }

    public function provideFindBlockEnd71Cases(): array
    {
        return [
            [10, '<?php use a\{ClassA, ClassB};', Tokens::BLOCK_TYPE_GROUP_IMPORT_BRACE, 5],
            [3, '<?php [$a] = $array;', Tokens::BLOCK_TYPE_DESTRUCTURING_SQUARE_BRACE, 1],
        ];
    }

    /**
     * @requires PHP 8.0
     * @dataProvider provideFindBlockEnd80Cases
     */
    public function testFindBlockEnd80(int $expectedIndex, string $source, int $type, int $searchIndex): void
    {
        static::assertFindBlockEnd($expectedIndex, $source, $type, $searchIndex);
    }

    public function provideFindBlockEnd80Cases(): array
    {
        return [
            [
                9,
                '<?php class Foo {
                    #[Required]
                    public $bar;
                }',
                Tokens::BLOCK_TYPE_ATTRIBUTE,
                7,
            ],
        ];
    }

    public function testFindBlockEndInvalidType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Invalid param type: "-1"\.$/');

        Tokens::clearCache();
        $tokens = Tokens::fromCode('<?php ');
        $tokens->findBlockEnd(-1, 0);
    }

    public function testFindBlockEndInvalidStart(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Invalid param \$startIndex - not a proper block "start"\.$/');

        Tokens::clearCache();
        $tokens = Tokens::fromCode('<?php ');
        $tokens->findBlockEnd(Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE, 0);
    }

    public function testFindBlockEndCalledMultipleTimes(): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode('<?php foo(1, 2);');

        static::assertSame(7, $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, 2));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Invalid param \$startIndex - not a proper block "start"\.$/');

        $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, 7);
    }

    public function testFindBlockStartEdgeCalledMultipleTimes(): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode('<?php foo(1, 2);');

        static::assertSame(2, $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, 7));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Invalid param \$startIndex - not a proper block "end"\.$/');

        $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, 2);
    }

    public function testEmptyTokens(): void
    {
        $code = '';
        $tokens = Tokens::fromCode($code);

        static::assertCount(0, $tokens);
        static::assertFalse($tokens->isTokenKindFound(T_OPEN_TAG));
    }

    public function testEmptyTokensMultiple(): void
    {
        $code = '';

        $tokens = Tokens::fromCode($code);
        static::assertFalse($tokens->isChanged());

        $tokens->insertAt(0, new Token([T_WHITESPACE, ' ']));
        static::assertCount(1, $tokens);
        static::assertFalse($tokens->isTokenKindFound(T_OPEN_TAG));
        static::assertTrue($tokens->isChanged());

        $tokens2 = Tokens::fromCode($code);
        static::assertCount(0, $tokens2);
        static::assertFalse($tokens->isTokenKindFound(T_OPEN_TAG));
    }

    public function testFromArray(): void
    {
        $code = '<?php echo 1;';

        $tokens1 = Tokens::fromCode($code);
        $tokens2 = Tokens::fromArray($tokens1->toArray());

        static::assertTrue($tokens1->isTokenKindFound(T_OPEN_TAG));
        static::assertTrue($tokens2->isTokenKindFound(T_OPEN_TAG));
        static::assertSame($tokens1->getCodeHash(), $tokens2->getCodeHash());
    }

    public function testFromArrayEmpty(): void
    {
        $tokens = Tokens::fromArray([]);
        static::assertFalse($tokens->isTokenKindFound(T_OPEN_TAG));
    }

    /**
     * @dataProvider provideIsEmptyCases
     */
    public function testIsEmpty(Token $token, bool $isEmpty): void
    {
        $tokens = Tokens::fromArray([$token]);
        Tokens::clearCache();
        static::assertSame($isEmpty, $tokens->isEmptyAt(0), $token->toJson());
    }

    public function provideIsEmptyCases(): array
    {
        return [
            [new Token(''), true],
            [new Token('('), false],
            [new Token([T_WHITESPACE, ' ']), false],
        ];
    }

    public function testClone(): void
    {
        $code = '<?php echo 1;';
        $tokens = Tokens::fromCode($code);

        $tokensClone = clone $tokens;

        static::assertTrue($tokens->isTokenKindFound(T_OPEN_TAG));
        static::assertTrue($tokensClone->isTokenKindFound(T_OPEN_TAG));

        $count = \count($tokens);
        static::assertCount($count, $tokensClone);

        for ($i = 0; $i < $count; ++$i) {
            static::assertTrue($tokens[$i]->equals($tokensClone[$i]));
            static::assertNotSame($tokens[$i], $tokensClone[$i]);
        }
    }

    /**
     * @param string $expected   valid PHP code
     * @param string $input      valid PHP code
     * @param int    $index      token index
     * @param string $whiteSpace white space
     *
     * @dataProvider provideEnsureWhitespaceAtIndexCases
     */
    public function testEnsureWhitespaceAtIndex(string $expected, string $input, int $index, int $offset, string $whiteSpace): void
    {
        $tokens = Tokens::fromCode($input);
        $tokens->ensureWhitespaceAtIndex($index, $offset, $whiteSpace);
        $tokens->clearEmptyTokens();
        static::assertTokens(Tokens::fromCode($expected), $tokens);
    }

    public function provideEnsureWhitespaceAtIndexCases(): array
    {
        return [
            [
                '<?php echo 1;',
                '<?php  echo 1;',
                1,
                1,
                ' ',
            ],
            [
                '<?php echo 7;',
                '<?php   echo 7;',
                1,
                1,
                ' ',
            ],
            [
                '<?php  ',
                '<?php  ',
                1,
                1,
                '  ',
            ],
            [
                '<?php $a. $b;',
                '<?php $a.$b;',
                2,
                1,
                ' ',
            ],
            [
                '<?php $a .$b;',
                '<?php $a.$b;',
                2,
                0,
                ' ',
            ],
            [
                "<?php\r\n",
                '<?php ',
                0,
                1,
                "\r\n",
            ],
            [
                '<?php  $a.$b;',
                '<?php $a.$b;',
                2,
                -1,
                ' ',
            ],
            [
                "<?php\t   ",
                "<?php\n",
                0,
                1,
                "\t   ",
            ],
            [
                '<?php ',
                '<?php ',
                0,
                1,
                ' ',
            ],
            [
                "<?php\n",
                '<?php ',
                0,
                1,
                "\n",
            ],
            [
                "<?php\t",
                '<?php ',
                0,
                1,
                "\t",
            ],
            [
                '<?php
//
 echo $a;',
                '<?php
//
echo $a;',
                2,
                1,
                "\n ",
            ],
            [
                '<?php
 echo $a;',
                '<?php
echo $a;',
                0,
                1,
                "\n ",
            ],
            [
                '<?php
echo $a;',
                '<?php echo $a;',
                0,
                1,
                "\n",
            ],
            [
                "<?php\techo \$a;",
                '<?php echo $a;',
                0,
                1,
                "\t",
            ],
        ];
    }

    public function testAssertTokensAfterChanging(): void
    {
        $template =
            '<?php class SomeClass {
                    %s//

                    public function __construct($name)
                    {
                        $this->name = $name;
                    }
            }';

        $tokens = Tokens::fromCode(sprintf($template, ''));
        $commentIndex = $tokens->getNextTokenOfKind(0, [[T_COMMENT]]);

        $tokens->insertAt(
            $commentIndex,
            [
                new Token([T_PRIVATE, 'private']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_VARIABLE, '$name']),
                new Token(';'),
            ]
        );

        static::assertTrue($tokens->isChanged());

        $expected = Tokens::fromCode(sprintf($template, 'private $name;'));
        static::assertFalse($expected->isChanged());

        static::assertTokens($expected, $tokens);
    }

    /**
     * @dataProvider provideRemoveLeadingWhitespaceCases
     */
    public function testRemoveLeadingWhitespace(int $index, ?string $whitespaces, string $expected, string $input = null): void
    {
        Tokens::clearCache();

        $tokens = Tokens::fromCode($input ?? $expected);
        $tokens->removeLeadingWhitespace($index, $whitespaces);

        static::assertSame($expected, $tokens->generateCode());
    }

    public function provideRemoveLeadingWhitespaceCases(): array
    {
        return [
            [
                7,
                null,
                "<?php echo 1;//\necho 2;",
            ],
            [
                7,
                null,
                "<?php echo 1;//\necho 2;",
                "<?php echo 1;//\n       echo 2;",
            ],
            [
                7,
                null,
                "<?php echo 1;//\r\necho 2;",
                "<?php echo 1;//\r\n       echo 2;",
            ],
            [
                7,
                " \t",
                "<?php echo 1;//\n//",
                "<?php echo 1;//\n       //",
            ],
            [
                6,
                "\t ",
                '<?php echo 1;//',
                "<?php echo 1;\t \t \t //",
            ],
            [
                8,
                null,
                '<?php $a = 1;//',
                '<?php $a = 1;           //',
            ],
            [
                6,
                null,
                '<?php echo 1;echo 2;',
                "<?php echo 1;  \n          \n \n     \necho 2;",
            ],
            [
                8,
                null,
                "<?php echo 1;  // 1\necho 2;",
                "<?php echo 1;  // 1\n          \n \n     \necho 2;",
            ],
        ];
    }

    /**
     * @dataProvider provideRemoveTrailingWhitespaceCases
     */
    public function testRemoveTrailingWhitespace(int $index, ?string $whitespaces, string $expected, string $input = null): void
    {
        Tokens::clearCache();

        $tokens = Tokens::fromCode($input ?? $expected);
        $tokens->removeTrailingWhitespace($index, $whitespaces);

        static::assertSame($expected, $tokens->generateCode());
    }

    public function provideRemoveTrailingWhitespaceCases(): \Generator
    {
        $leadingCases = $this->provideRemoveLeadingWhitespaceCases();

        foreach ($leadingCases as $leadingCase) {
            $leadingCase[0] -= 2;
            yield $leadingCase;
        }
    }

    public function testRemovingLeadingWhitespaceWithEmptyTokenInCollection(): void
    {
        $code = "<?php\n    /* I will be removed */MY_INDEX_IS_THREE;foo();";
        $tokens = Tokens::fromCode($code);
        $tokens->clearAt(2);

        $tokens->removeLeadingWhitespace(3);

        $tokens->clearEmptyTokens();
        static::assertTokens(Tokens::fromCode("<?php\nMY_INDEX_IS_THREE;foo();"), $tokens);
    }

    public function testRemovingTrailingWhitespaceWithEmptyTokenInCollection(): void
    {
        $code = "<?php\nMY_INDEX_IS_ONE/* I will be removed */    ;foo();";
        $tokens = Tokens::fromCode($code);
        $tokens->clearAt(2);

        $tokens->removeTrailingWhitespace(1);

        $tokens->clearEmptyTokens();
        static::assertTokens(Tokens::fromCode("<?php\nMY_INDEX_IS_ONE;foo();"), $tokens);
    }

    /**
     * Action that begins with the word "remove" should not change the size of collection.
     */
    public function testRemovingLeadingWhitespaceWillNotIncreaseTokensCount(): void
    {
        $tokens = Tokens::fromCode('<?php
                                    // Foo
                                    $bar;');
        $originalCount = $tokens->count();

        $tokens->removeLeadingWhitespace(4);

        static::assertSame($originalCount, $tokens->count());
        static::assertSame(
            '<?php
                                    // Foo
$bar;',
            $tokens->generateCode()
        );
    }

    /**
     * Action that begins with the word "remove" should not change the size of collection.
     */
    public function testRemovingTrailingWhitespaceWillNotIncreaseTokensCount(): void
    {
        $tokens = Tokens::fromCode('<?php
                                    // Foo
                                    $bar;');
        $originalCount = $tokens->count();

        $tokens->removeTrailingWhitespace(2);

        static::assertSame($originalCount, $tokens->count());
        static::assertSame(
            '<?php
                                    // Foo
$bar;',
            $tokens->generateCode()
        );
    }

    /**
     * @dataProvider provideDetectBlockTypeCases
     */
    public function testDetectBlockType(?array $expected, string $code, int $index): void
    {
        $tokens = Tokens::fromCode($code);
        static::assertSame($expected, Tokens::detectBlockType($tokens[$index]));
    }

    public function provideDetectBlockTypeCases(): \Generator
    {
        yield [
            [
                'type' => Tokens::BLOCK_TYPE_CURLY_BRACE,
                'isStart' => true,
            ],
            '<?php { echo 1; }',
            1,
        ];

        yield [
            null,
            '<?php { echo 1;}',
            0,
        ];
    }

    public function testOverrideRangeTokens(): void
    {
        $expected = [
            new Token([T_OPEN_TAG, '<?php ']),
            new Token([T_FUNCTION, 'function']),
            new Token([T_WHITESPACE, ' ']),
            new Token([T_STRING, 'foo']),
            new Token('('),
            new Token([T_ARRAY, 'array']),
            new Token([T_WHITESPACE, ' ']),
            new Token([T_VARIABLE, '$bar']),
            new Token(')'),
            new Token('{'),
            new Token('}'),
        ];
        $code = '<?php function foo(array $bar){}';
        $indexStart = 5;
        $indexEnd = 5;
        $items = Tokens::fromArray([new Token([T_ARRAY, 'array'])]);

        $tokens = Tokens::fromCode($code);
        $tokens->overrideRange($indexStart, $indexEnd, $items);
        $tokens->clearEmptyTokens();

        self::assertTokens(Tokens::fromArray($expected), $tokens);
    }

    /**
     * @param int          $indexStart start overriding index
     * @param int          $indexEnd   end overriding index
     * @param array<Token> $items      tokens to insert
     *
     * @dataProvider provideOverrideRangeCases
     */
    public function testOverrideRange(array $expected, string $code, int $indexStart, int $indexEnd, array $items): void
    {
        $tokens = Tokens::fromCode($code);
        $tokens->overrideRange($indexStart, $indexEnd, $items);
        $tokens->clearEmptyTokens();

        self::assertTokens(Tokens::fromArray($expected), $tokens);
    }

    public function provideOverrideRangeCases(): \Generator
    {
        // typically done by transformers, here we test the reverse

        yield 'override different tokens but same content' => [
            [
                new Token([T_OPEN_TAG, '<?php ']),
                new Token([T_FUNCTION, 'function']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, 'foo']),
                new Token('('),
                new Token([T_ARRAY, 'array']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_VARIABLE, '$bar']),
                new Token(')'),
                new Token('{'),
                new Token('}'),
            ],
            '<?php function foo(array $bar){}',
            5,
            5,
            [new Token([T_ARRAY, 'array'])],
        ];

        yield 'add more item than in range' => [
            [
                new Token([T_OPEN_TAG, "<?php\n"]),
                new Token([T_COMMENT, '// test']),
                new Token([T_WHITESPACE, "\n"]),
                new Token([T_COMMENT, '// test']),
                new Token([T_WHITESPACE, "\n"]),
                new Token([T_COMMENT, '// test']),
                new Token([T_WHITESPACE, "\n"]),
            ],
            "<?php\n#comment",
            1,
            1,
            [
                new Token([T_COMMENT, '// test']),
                new Token([T_WHITESPACE, "\n"]),
                new Token([T_COMMENT, '// test']),
                new Token([T_WHITESPACE, "\n"]),
                new Token([T_COMMENT, '// test']),
                new Token([T_WHITESPACE, "\n"]),
            ],
        ];

        yield [
            [
                new Token([T_OPEN_TAG, "<?php\n"]),
                new Token([T_COMMENT, '#comment1']),
                new Token([T_WHITESPACE, "\n"]),
                new Token([T_COMMENT, '// test 1']),
                new Token([T_WHITESPACE, "\n"]),
                new Token([T_COMMENT, '#comment5']),
                new Token([T_WHITESPACE, "\n"]),
                new Token([T_COMMENT, '#comment6']),
            ],
            "<?php\n#comment1\n#comment2\n#comment3\n#comment4\n#comment5\n#comment6",
            3,
            7,
            [
                new Token([T_COMMENT, '// test 1']),
            ],
        ];

        yield [
            [
                new Token([T_OPEN_TAG, "<?php\n"]),
                new Token([T_COMMENT, '// test']),
            ],
            "<?php\n#comment1\n#comment2\n#comment3\n#comment4\n#comment5\n#comment6\n#comment7",
            1,
            13,
            [
                new Token([T_COMMENT, '// test']),
            ],
        ];

        yield [
            [
                new Token([T_OPEN_TAG, "<?php\n"]),
                new Token([T_COMMENT, '// test']),
            ],
            "<?php\n#comment",
            1,
            1,
            [
                new Token([T_COMMENT, '// test']),
            ],
        ];
    }

    public function testInitialChangedState(): void
    {
        $tokens = Tokens::fromCode("<?php\n");
        static::assertFalse($tokens->isChanged());

        $tokens = Tokens::fromArray(
            [
                new Token([T_OPEN_TAG, "<?php\n"]),
                new Token([T_STRING, 'Foo']),
                new Token(';'),
            ]
        );
        static::assertFalse($tokens->isChanged());
    }

    /**
     * @dataProvider provideGetMeaningfulTokenSiblingCases
     */
    public function testGetMeaningfulTokenSibling(?int $expectIndex, int $index, int $direction, string $source): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        static::assertSame($expectIndex, $tokens->getMeaningfulTokenSibling($index, $direction));
    }

    public function provideGetMeaningfulTokenSiblingCases(): \Generator
    {
        yield [null, 0, 1, '<?php '];

        yield [null, 1, 1, '<?php /**/ /**/ /**/ /**/#'];

        for ($i = 0; $i < 3; ++$i) {
            yield '>'.$i => [3, $i, 1, '<?php /**/ foo();'];
        }

        yield '>>' => [4, 3, 1, '<?php /**/ foo();'];
        yield '@ end' => [null, 6, 1, '<?php /**/ foo();'];
        yield 'over end' => [null, 888, 1, '<?php /**/ foo();'];

        yield [0, 3, -1, '<?php /**/ foo();'];
        yield [4, 5, -1, '<?php /**/ foo();'];
        yield [5, 6, -1, '<?php /**/ foo();'];
        yield [null, 0, -1, '<?php /**/ foo();'];
    }

    private static function assertFindBlockEnd(int $expectedIndex, string $source, int $type, int $searchIndex): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        static::assertSame($expectedIndex, $tokens->findBlockEnd($type, $searchIndex));
        static::assertSame($searchIndex, $tokens->findBlockStart($type, $expectedIndex));

        $detectedType = Tokens::detectBlockType($tokens[$searchIndex]);
        static::assertIsArray($detectedType);
        static::assertArrayHasKey('type', $detectedType);
        static::assertArrayHasKey('isStart', $detectedType);
        static::assertSame($type, $detectedType['type']);
        static::assertTrue($detectedType['isStart']);

        $detectedType = Tokens::detectBlockType($tokens[$expectedIndex]);
        static::assertIsArray($detectedType);
        static::assertArrayHasKey('type', $detectedType);
        static::assertArrayHasKey('isStart', $detectedType);
        static::assertSame($type, $detectedType['type']);
        static::assertFalse($detectedType['isStart']);
    }

    /**
     * @param null|Token[] $expected
     * @param null|Token[] $input
     */
    private static function assertEqualsTokensArray(array $expected = null, array $input = null): void
    {
        if (null === $expected) {
            static::assertNull($input);

            return;
        }

        if (null === $input) {
            static::fail('While "input" is <null>, "expected" is not.');
        }

        static::assertSame(array_keys($expected), array_keys($input), 'Both arrays need to have same keys.');

        foreach ($expected as $index => $expectedToken) {
            static::assertTrue(
                $expectedToken->equals($input[$index]),
                sprintf('The token at index %d should be %s, got %s', $index, $expectedToken->toJson(), $input[$index]->toJson())
            );
        }
    }

    /**
     * @param int[]   $indexes
     * @param Token[] $expected
     */
    private function doTestClearTokens(string $source, array $indexes, array $expected): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);
        foreach ($indexes as $index) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }

        static::assertSame(\count($expected), $tokens->count());
        foreach ($expected as $index => $expectedToken) {
            $token = $tokens[$index];
            $expectedPrototype = $expectedToken->getPrototype();
            if (\is_array($expectedPrototype)) {
                unset($expectedPrototype[2]); // don't compare token lines as our token mutations don't deal with line numbers
            }

            static::assertTrue($token->equals($expectedPrototype), sprintf('The token at index %d should be %s, got %s', $index, json_encode($expectedPrototype), $token->toJson()));
        }
    }
}
