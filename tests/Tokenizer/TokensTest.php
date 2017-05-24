<?php

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

use PhpCsFixer\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

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

    public function testReadFromCacheAfterClearing()
    {
        $code = '<?php echo 1;';
        $tokens = Tokens::fromCode($code);

        $countBefore = $tokens->count();

        for ($i = 0; $i < $countBefore; ++$i) {
            $tokens[$i]->clear();
        }

        $tokens = Tokens::fromCode($code);

        $this->assertSame($countBefore, $tokens->count());
    }

    /**
     * @param string     $source
     * @param null|array $expected
     * @param Token[]    $sequence
     * @param int        $start
     * @param int|null   $end
     * @param bool|array $caseSensitive
     *
     * @dataProvider provideFindSequence
     */
    public function testFindSequence(
        $source,
        array $expected = null,
        array $sequence,
        $start = 0,
        $end = null,
        $caseSensitive = true
    ) {
        $tokens = Tokens::fromCode($source);

        $this->assertEqualsTokensArray(
            $expected,
            $tokens->findSequence(
                $sequence,
                $start,
                $end,
                $caseSensitive
            )
        );
    }

    public function provideFindSequence()
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
                [1, true],
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
                [1, false],
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
     * @param string $message
     * @param array  $sequence
     *
     * @dataProvider provideFindSequenceExceptions
     */
    public function testFindSequenceException($message, array $sequence)
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            $message
        );

        $tokens = Tokens::fromCode('<?php $x = 1;');
        $tokens->findSequence($sequence);
    }

    public function provideFindSequenceExceptions()
    {
        $emptyToken = new Token('!');
        $emptyToken->clear();

        return [
            ['Invalid sequence.', []],
            ['Non-meaningful token at position: 0.', [
                [T_WHITESPACE, '   '],
            ]],
            ['Non-meaningful token at position: 1.', [
                '{', [T_COMMENT, '// Foo'], '}',
            ]],
            ['Non-meaningful token at position: 2.', [
                '{', '!', $emptyToken, '}',
            ]],
        ];
    }

    public function testClearRange()
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
        list($fooIndex, $barIndex) = array_keys($tokens->findGivenKind(T_PUBLIC));

        $tokens->clearRange($fooIndex, $barIndex - 1);

        $newPublicIndexes = array_keys($tokens->findGivenKind(T_PUBLIC));
        $this->assertSame($barIndex, reset($newPublicIndexes));

        for ($i = $fooIndex; $i < $barIndex; ++$i) {
            $this->assertTrue($tokens[$i]->isWhitespace());
        }
    }

    /**
     * @dataProvider provideMonolithicPhpDetection
     *
     * @param string $source
     * @param bool   $monolithic
     */
    public function testMonolithicPhpDetection($source, $monolithic)
    {
        $tokens = Tokens::fromCode($source);
        $this->assertSame($monolithic, $tokens->isMonolithicPhp());
    }

    public function provideMonolithicPhpDetection()
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
     * @dataProvider provideShortOpenTagMonolithicPhpDetection
     *
     * @param string $source
     * @param bool   $monolithic
     */
    public function testShortOpenTagMonolithicPhpDetection($source, $monolithic)
    {
        if (!ini_get('short_open_tag')) {
            $monolithic = false;
        }

        $tokens = Tokens::fromCode($source);
        $this->assertSame($monolithic, $tokens->isMonolithicPhp());
    }

    public function provideShortOpenTagMonolithicPhpDetection()
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
     * @dataProvider provideShortOpenTagEchoMonolithicPhpDetection
     *
     * @param string $source
     * @param bool   $monolithic
     */
    public function testShortOpenTagEchoMonolithicPhpDetection($source, $monolithic)
    {
        $tokens = Tokens::fromCode($source);
        $this->assertSame($monolithic, $tokens->isMonolithicPhp());
    }

    public function provideShortOpenTagEchoMonolithicPhpDetection()
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

    public function testTokenKindsFound()
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

        $this->assertTrue($tokens->isTokenKindFound(T_CLASS));
        $this->assertTrue($tokens->isTokenKindFound(T_RETURN));
        $this->assertFalse($tokens->isTokenKindFound(T_INTERFACE));
        $this->assertFalse($tokens->isTokenKindFound(T_ARRAY));

        $this->assertTrue($tokens->isAllTokenKindsFound([T_CLASS, T_RETURN]));
        $this->assertFalse($tokens->isAllTokenKindsFound([T_CLASS, T_INTERFACE]));

        $this->assertTrue($tokens->isAnyTokenKindsFound([T_CLASS, T_RETURN]));
        $this->assertTrue($tokens->isAnyTokenKindsFound([T_CLASS, T_INTERFACE]));
        $this->assertFalse($tokens->isAnyTokenKindsFound([T_INTERFACE, T_ARRAY]));
    }

    public function testFindGivenKind()
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
        $this->assertInternalType('array', $found);
        $this->assertCount(1, $found);
        $this->assertArrayHasKey(1, $found);
        $this->assertSame(T_CLASS, $found[1]->getId());

        /** @var array $found */
        $found = $tokens->findGivenKind([T_CLASS, T_FUNCTION]);
        $this->assertCount(2, $found);
        $this->assertArrayHasKey(T_CLASS, $found);
        $this->assertInternalType('array', $found[T_CLASS]);
        $this->assertCount(1, $found[T_CLASS]);
        $this->assertArrayHasKey(1, $found[T_CLASS]);
        $this->assertSame(T_CLASS, $found[T_CLASS][1]->getId());

        $this->assertArrayHasKey(T_FUNCTION, $found);
        $this->assertInternalType('array', $found[T_FUNCTION]);
        $this->assertCount(2, $found[T_FUNCTION]);
        $this->assertArrayHasKey(9, $found[T_FUNCTION]);
        $this->assertSame(T_FUNCTION, $found[T_FUNCTION][9]->getId());
        $this->assertArrayHasKey(26, $found[T_FUNCTION]);
        $this->assertSame(T_FUNCTION, $found[T_FUNCTION][26]->getId());

        // test offset and limits of the search
        $found = $tokens->findGivenKind([T_CLASS, T_FUNCTION], 10);
        $this->assertCount(0, $found[T_CLASS]);
        $this->assertCount(1, $found[T_FUNCTION]);
        $this->assertArrayHasKey(26, $found[T_FUNCTION]);

        $found = $tokens->findGivenKind([T_CLASS, T_FUNCTION], 2, 10);
        $this->assertCount(0, $found[T_CLASS]);
        $this->assertCount(1, $found[T_FUNCTION]);
        $this->assertArrayHasKey(9, $found[T_FUNCTION]);
    }

    /**
     * @param string  $source
     * @param Token[] $expected tokens
     * @param int[]   $indexes  to clear
     *
     * @dataProvider getClearTokenAndMergeSurroundingWhitespaceCases
     */
    public function testClearTokenAndMergeSurroundingWhitespace($source, array $indexes, array $expected)
    {
        $this->doTestClearTokens($source, $indexes, $expected);
        if (count($indexes) > 1) {
            $this->doTestClearTokens($source, array_reverse($indexes), $expected);
        }
    }

    public function getClearTokenAndMergeSurroundingWhitespaceCases()
    {
        $clearToken = new Token([null, '']);
        $clearToken->clear();

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
     * @param int   $expectedIndex
     * @param int   $direction
     * @param int   $index
     * @param array $findTokens
     * @param bool  $caseSensitive
     *
     * @dataProvider provideTokenOfKindSiblingCases
     */
    public function testTokenOfKindSibling(
        $expectedIndex,
        $direction,
        $index,
        array $findTokens,
        $caseSensitive = true
    ) {
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
            $this->assertSame($expectedIndex, $tokens->getNextTokenOfKind($index, $findTokens, $caseSensitive));
        } else {
            $this->assertSame($expectedIndex, $tokens->getPrevTokenOfKind($index, $findTokens, $caseSensitive));
        }

        $this->assertSame($expectedIndex, $tokens->getTokenOfKindSibling($index, $direction, $findTokens, $caseSensitive));
    }

    public function provideTokenOfKindSiblingCases()
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
     * @param int    $expectedIndex
     * @param string $source
     * @param int    $type
     * @param int    $searchIndex
     *
     * @dataProvider provideFindBlockEndCases
     */
    public function testFindBlockEnd($expectedIndex, $source, $type, $searchIndex)
    {
        $this->assertFindBlockEnd($expectedIndex, $source, $type, $searchIndex);
    }

    public function provideFindBlockEndCases()
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
        ];
    }

    /**
     * @param int    $expectedIndex
     * @param string $source
     * @param int    $type
     * @param int    $searchIndex
     *
     * @requires PHP 7.1
     * @dataProvider provideFindBlockEndCases71
     */
    public function testFindBlockEnd71($expectedIndex, $source, $type, $searchIndex)
    {
        $this->assertFindBlockEnd($expectedIndex, $source, $type, $searchIndex);
    }

    public function provideFindBlockEndCases71()
    {
        return [
            [10, '<?php use a\{ClassA, ClassB};', Tokens::BLOCK_TYPE_GROUP_IMPORT_BRACE, 5],
            [3, '<?php [$a] = $array;', Tokens::BLOCK_TYPE_DESTRUCTURING_SQUARE_BRACE, 1],
        ];
    }

    public function testFindBlockEndInvalidType()
    {
        $this->setExpectedExceptionRegExp(
            \InvalidArgumentException::class,
            '/^Invalid param type: -1\.$/'
        );

        Tokens::clearCache();
        $tokens = Tokens::fromCode('<?php ');
        $tokens->findBlockEnd(-1, 0);
    }

    public function testFindBlockEndInvalidStart()
    {
        $this->setExpectedExceptionRegExp(
            \InvalidArgumentException::class,
            '/^Invalid param \$startIndex - not a proper block start\.$/'
        );

        Tokens::clearCache();
        $tokens = Tokens::fromCode('<?php ');
        $tokens->findBlockEnd(Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE, 0);
    }

    public function testEmptyTokens()
    {
        $code = '';
        $tokens = Tokens::fromCode($code);

        $this->assertCount(0, $tokens);
        $this->assertFalse($tokens->isTokenKindFound(T_OPEN_TAG));
    }

    public function testEmptyTokensMultiple()
    {
        $code = '';

        $tokens = Tokens::fromCode($code);
        $tokens->insertAt(0, new Token([T_WHITESPACE, ' ']));
        $this->assertCount(1, $tokens);
        $this->assertFalse($tokens->isTokenKindFound(T_OPEN_TAG));

        $tokens2 = Tokens::fromCode($code);
        $this->assertCount(0, $tokens2);
        $this->assertFalse($tokens->isTokenKindFound(T_OPEN_TAG));
    }

    public function testFromArray()
    {
        $code = '<?php echo 1;';

        $tokens1 = Tokens::fromCode($code);
        $tokens2 = Tokens::fromArray($tokens1->toArray());

        $this->assertTrue($tokens1->isTokenKindFound(T_OPEN_TAG));
        $this->assertTrue($tokens2->isTokenKindFound(T_OPEN_TAG));
        $this->assertSame($tokens1->getCodeHash(), $tokens2->getCodeHash());
    }

    public function testFromArrayEmpty()
    {
        $tokens = Tokens::fromArray([]);
        $this->assertFalse($tokens->isTokenKindFound(T_OPEN_TAG));
    }

    public function testClone()
    {
        $code = '<?php echo 1;';
        $tokens = Tokens::fromCode($code);

        $tokensClone = clone $tokens;

        $this->assertTrue($tokens->isTokenKindFound(T_OPEN_TAG));
        $this->assertTrue($tokensClone->isTokenKindFound(T_OPEN_TAG));

        $count = count($tokens);
        $this->assertCount($count, $tokensClone);

        for ($i = 0; $i < $count; ++$i) {
            $this->assertTrue($tokens[$i]->equals($tokensClone[$i]));
            $this->assertNotSame($tokens[$i], $tokensClone[$i]);
        }
    }

    /**
     * @param int    $expectedIndex
     * @param string $source
     * @param int    $type
     * @param int    $searchIndex
     */
    public function assertFindBlockEnd($expectedIndex, $source, $type, $searchIndex)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        $this->assertSame($expectedIndex, $tokens->findBlockEnd($type, $searchIndex, true));
        $this->assertSame($searchIndex, $tokens->findBlockEnd($type, $expectedIndex, false));

        $detectedType = Tokens::detectBlockType($tokens[$searchIndex]);
        $this->assertInternalType('array', $detectedType);
        $this->assertArrayHasKey('type', $detectedType);
        $this->assertArrayHasKey('isStart', $detectedType);
        $this->assertSame($type, $detectedType['type']);
        $this->assertTrue($detectedType['isStart']);

        $detectedType = Tokens::detectBlockType($tokens[$expectedIndex]);
        $this->assertInternalType('array', $detectedType);
        $this->assertArrayHasKey('type', $detectedType);
        $this->assertArrayHasKey('isStart', $detectedType);
        $this->assertSame($type, $detectedType['type']);
        $this->assertFalse($detectedType['isStart']);
    }

    /**
     * @param string $expected   valid PHP code
     * @param string $input      valid PHP code
     * @param int    $index      token index
     * @param int    $offset
     * @param string $whiteSpace white space
     *
     * @dataProvider provideEnsureWhitespaceAtIndexCases
     */
    public function testEnsureWhitespaceAtIndex($expected, $input, $index, $offset, $whiteSpace)
    {
        $tokens = Tokens::fromCode($input);
        $tokens->ensureWhitespaceAtIndex($index, $offset, $whiteSpace);

        $this->assertTokens(Tokens::fromCode($expected), $tokens);
    }

    public function provideEnsureWhitespaceAtIndexCases()
    {
        return [
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

    /**
     * @param null|Token[] $expected
     * @param null|Token[] $input
     */
    private function assertEqualsTokensArray(array $expected = null, array $input = null)
    {
        if (null === $expected) {
            $this->assertNull($input);

            return;
        } elseif (null === $input) {
            $this->fail('While "input" is <null>, "expected" is not.');
        }

        $this->assertSame(array_keys($expected), array_keys($input), 'Both arrays need to have same keys.');

        foreach ($expected as $index => $expectedToken) {
            $this->assertTrue(
                $expectedToken->equals($input[$index]),
                sprintf('The token at index %d should be %s, got %s', $index, $expectedToken->toJson(), $input[$index]->toJson())
            );
        }
    }

    /**
     * @param string  $source
     * @param int[]   $indexes
     * @param Token[] $expected
     */
    private function doTestClearTokens($source, array $indexes, array $expected)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);
        foreach ($indexes as $index) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }

        $this->assertSame(count($expected), $tokens->count());
        foreach ($expected as $index => $expectedToken) {
            $token = $tokens[$index];
            $expectedPrototype = $expectedToken->getPrototype();
            if (is_array($expectedPrototype)) {
                unset($expectedPrototype[2]); // don't compare token lines as our token mutations don't deal with line numbers
            }

            $this->assertTrue($token->equals($expectedPrototype), sprintf('The token at index %d should be %s, got %s', $index, json_encode($expectedPrototype), $token->toJson()));
        }
    }
}
