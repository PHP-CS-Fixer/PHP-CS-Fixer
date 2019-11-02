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

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Token
 */
final class TokenTest extends TestCase
{
    /**
     * @param mixed $input
     *
     * @dataProvider provideConstructorValidationCases
     */
    public function testConstructorValidation($input)
    {
        $this->expectException(\InvalidArgumentException::class);

        new Token($input);
    }

    public function provideConstructorValidationCases()
    {
        return [
            [null],
            [123],
            [new \stdClass()],
            [['asd', 'asd']],
            [[null, 'asd']],
            [[new \stdClass(), 'asd']],
            [[T_WHITESPACE, null]],
            [[T_WHITESPACE, 123]],
            [[T_WHITESPACE, '']],
            [[T_WHITESPACE, new \stdClass()]],
        ];
    }

    public function testGetPrototype()
    {
        static::assertSame($this->getBraceTokenPrototype(), $this->getBraceToken()->getPrototype());
        static::assertSame($this->getForeachTokenPrototype(), $this->getForeachToken()->getPrototype());
    }

    public function testIsArray()
    {
        static::assertFalse($this->getBraceToken()->isArray());
        static::assertTrue($this->getForeachToken()->isArray());
    }

    /**
     * @param bool $isCast
     *
     * @dataProvider provideIsCastCases
     */
    public function testIsCast(Token $token, $isCast)
    {
        static::assertSame($isCast, $token->isCast());
    }

    public function provideIsCastCases()
    {
        return [
            [$this->getBraceToken(), false],
            [$this->getForeachToken(), false],
            [new Token([T_ARRAY_CAST, '(array)', 1]), true],
            [new Token([T_BOOL_CAST, '(bool)', 1]), true],
            [new Token([T_DOUBLE_CAST, '(double)', 1]), true],
            [new Token([T_INT_CAST, '(int)', 1]), true],
            [new Token([T_OBJECT_CAST, '(object)', 1]), true],
            [new Token([T_STRING_CAST, '(string)', 1]), true],
            [new Token([T_UNSET_CAST, '(unset)', 1]), true],
        ];
    }

    /**
     * @param bool $isClassy
     *
     * @dataProvider provideIsClassyCases
     */
    public function testIsClassy(Token $token, $isClassy)
    {
        static::assertSame($isClassy, $token->isClassy());
    }

    public function provideIsClassyCases()
    {
        return [
            [$this->getBraceToken(), false],
            [$this->getForeachToken(), false],
            [new Token([T_CLASS, 'class', 1]), true],
            [new Token([T_INTERFACE, 'interface', 1]), true],
            [new Token([T_TRAIT, 'trait', 1]), true],
        ];
    }

    /**
     * @param bool $isComment
     *
     * @dataProvider provideIsCommentCases
     */
    public function testIsComment(Token $token, $isComment)
    {
        static::assertSame($isComment, $token->isComment());
    }

    public function provideIsCommentCases()
    {
        return [
            [$this->getBraceToken(), false],
            [$this->getForeachToken(), false],
            [new Token([T_COMMENT, '/* comment */', 1]), true],
            [new Token([T_DOC_COMMENT, '/** docs */', 1]), true],
        ];
    }

    public function testIsGivenKind()
    {
        $braceToken = $this->getBraceToken();
        $foreachToken = $this->getForeachToken();

        static::assertFalse($braceToken->isGivenKind(T_FOR));
        static::assertFalse($braceToken->isGivenKind(T_FOREACH));
        static::assertFalse($braceToken->isGivenKind([T_FOR]));
        static::assertFalse($braceToken->isGivenKind([T_FOREACH]));
        static::assertFalse($braceToken->isGivenKind([T_FOR, T_FOREACH]));

        static::assertFalse($foreachToken->isGivenKind(T_FOR));
        static::assertTrue($foreachToken->isGivenKind(T_FOREACH));
        static::assertFalse($foreachToken->isGivenKind([T_FOR]));
        static::assertTrue($foreachToken->isGivenKind([T_FOREACH]));
        static::assertTrue($foreachToken->isGivenKind([T_FOR, T_FOREACH]));
    }

    public function testIsKeywords()
    {
        static::assertTrue($this->getForeachToken()->isKeyword());
        static::assertFalse($this->getBraceToken()->isKeyword());
    }

    /**
     * @param ?int   $tokenId
     * @param string $content
     * @param bool   $isConstant
     *
     * @dataProvider provideMagicConstantCases
     */
    public function testIsMagicConstant($tokenId, $content, $isConstant = true)
    {
        $token = new Token(
            null === $tokenId ? $content : [$tokenId, $content]
        );

        static::assertSame($isConstant, $token->isMagicConstant());
    }

    public function provideMagicConstantCases()
    {
        $cases = [
            [T_CLASS_C, '__CLASS__'],
            [T_DIR, '__DIR__'],
            [T_FILE, '__FILE__'],
            [T_FUNC_C, '__FUNCTION__'],
            [T_LINE, '__LINE__'],
            [T_METHOD_C, '__METHOD__'],
            [T_NS_C, '__NAMESPACE__'],
            [T_TRAIT_C, '__TRAIT__'],
        ];

        foreach ($cases as $case) {
            $cases[] = [$case[0], strtolower($case[1])];
        }

        foreach ([$this->getForeachToken(), $this->getBraceToken()] as $token) {
            $cases[] = [$token->getId(), $token->getContent(), false];
            $cases[] = [$token->getId(), strtolower($token->getContent()), false];
        }

        return $cases;
    }

    /**
     * @param bool $isNativeConstant
     *
     * @dataProvider provideIsNativeConstantCases
     */
    public function testIsNativeConstant(Token $token, $isNativeConstant)
    {
        static::assertSame($isNativeConstant, $token->isNativeConstant());
    }

    public function provideIsNativeConstantCases()
    {
        return [
            [$this->getBraceToken(), false],
            [$this->getForeachToken(), false],
            [new Token([T_STRING, 'null', 1]), true],
            [new Token([T_STRING, 'false', 1]), true],
            [new Token([T_STRING, 'true', 1]), true],
            [new Token([T_STRING, 'tRuE', 1]), true],
            [new Token([T_STRING, 'TRUE', 1]), true],
        ];
    }

    /**
     * @param bool        $isWhitespace
     * @param null|string $whitespaces
     *
     * @dataProvider provideIsWhitespaceCases
     */
    public function testIsWhitespace(Token $token, $isWhitespace, $whitespaces = null)
    {
        if (null !== $whitespaces) {
            static::assertSame($isWhitespace, $token->isWhitespace($whitespaces));
        } else {
            static::assertSame($isWhitespace, $token->isWhitespace());
        }
    }

    public function provideIsWhitespaceCases()
    {
        return [
            [$this->getBraceToken(), false],
            [$this->getForeachToken(), false],
            [new Token(' '), true],
            [new Token("\t "), true],
            [new Token("\t "), false, ' '],
            [new Token([T_WHITESPACE, "\r", 1]), true],
            [new Token([T_WHITESPACE, "\0", 1]), true],
            [new Token([T_WHITESPACE, "\x0B", 1]), true],
            [new Token([T_WHITESPACE, "\n", 1]), true],
            [new Token([T_WHITESPACE, "\n", 1]), false, " \t"],
        ];
    }

    /**
     * @param mixed       $prototype
     * @param null|int    $expectedId
     * @param null|string $expectedContent
     * @param null|bool   $expectedIsArray
     * @param null|string $expectedExceptionClass
     *
     * @dataProvider provideCreatingTokenCases
     */
    public function testCreatingToken($prototype, $expectedId, $expectedContent, $expectedIsArray, $expectedExceptionClass = null)
    {
        if ($expectedExceptionClass) {
            $this->expectException($expectedExceptionClass);
        }

        $token = new Token($prototype);
        static::assertSame($expectedId, $token->getId());
        static::assertSame($expectedContent, $token->getContent());
        static::assertSame($expectedIsArray, $token->isArray());
    }

    public function provideCreatingTokenCases()
    {
        return [
            [[T_FOREACH, 'foreach'], T_FOREACH, 'foreach', true],
            ['(', null, '(', false],
            [123, null, null, null, \InvalidArgumentException::class],
            [false, null, null, null, \InvalidArgumentException::class],
            [null, null, null, null, \InvalidArgumentException::class],
        ];
    }

    public function testEqualsDefaultIsCaseSensitive()
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        static::assertTrue($token->equals([T_FUNCTION, 'function']));
        static::assertFalse($token->equals([T_FUNCTION, 'Function']));
    }

    /**
     * @param string             $equals
     * @param array|string|Token $other
     * @param bool               $caseSensitive
     *
     * @dataProvider provideEqualsCases
     */
    public function testEquals(Token $token, $equals, $other, $caseSensitive = true)
    {
        static::assertSame($equals, $token->equals($other, $caseSensitive));
    }

    public function provideEqualsCases()
    {
        $brace = $this->getBraceToken();
        $function = new Token([T_FUNCTION, 'function', 1]);

        return [
            [$brace, false, '!'],
            [$brace, false, '!', false],
            [$brace, true, '('],
            [$brace, true, '(', false],
            [$function, false, '('],
            [$function, false, '(', false],

            [$function, false, [T_NAMESPACE]],
            [$function, false, [T_NAMESPACE], false],
            [$function, false, [T_VARIABLE, 'function']],
            [$function, false, [T_VARIABLE, 'function'], false],
            [$function, false, [T_VARIABLE, 'Function']],
            [$function, false, [T_VARIABLE, 'Function'], false],
            [$function, true, [T_FUNCTION]],
            [$function, true, [T_FUNCTION], false],
            [$function, true, [T_FUNCTION, 'function']],
            [$function, true, [T_FUNCTION, 'function'], false],
            [$function, false, [T_FUNCTION, 'Function']],
            [$function, true, [T_FUNCTION, 'Function'], false],
            [$function, false, [T_FUNCTION, 'junction'], false],

            [$function, true, new Token([T_FUNCTION, 'function'])],
            [$function, false, new Token([T_FUNCTION, 'Function'])],
            [$function, true, new Token([T_FUNCTION, 'Function']), false],

            // if it is an array any additional field is checked too
            [$function, false, [T_FUNCTION, 'function', 'unexpected']],
        ];
    }

    public function testEqualsAnyDefaultIsCaseSensitive()
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        static::assertTrue($token->equalsAny([[T_FUNCTION, 'function']]));
        static::assertFalse($token->equalsAny([[T_FUNCTION, 'Function']]));
    }

    /**
     * @param bool $equalsAny
     * @param bool $caseSensitive
     *
     * @dataProvider provideEqualsAnyCases
     */
    public function testEqualsAny($equalsAny, array $other, $caseSensitive = true)
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        static::assertSame($equalsAny, $token->equalsAny($other, $caseSensitive));
    }

    public function provideEqualsAnyCases()
    {
        $brace = $this->getBraceToken();
        $foreach = $this->getForeachToken();

        return [
            [false, []],
            [false, [$brace]],
            [false, [$brace, $foreach]],
            [true, [$brace, $foreach, [T_FUNCTION]]],
            [true, [$brace, $foreach, [T_FUNCTION, 'function']]],
            [false, [$brace, $foreach, [T_FUNCTION, 'Function']]],
            [true, [$brace, $foreach, [T_FUNCTION, 'Function']], false],
            [false, [[T_VARIABLE, 'junction'], [T_FUNCTION, 'junction']], false],
        ];
    }

    /**
     * @param bool       $isKeyCaseSensitive
     * @param array|bool $caseSensitive
     * @param int        $key
     *
     * @dataProvider provideIsKeyCaseSensitiveCases
     */
    public function testIsKeyCaseSensitive($isKeyCaseSensitive, $caseSensitive, $key)
    {
        static::assertSame($isKeyCaseSensitive, Token::isKeyCaseSensitive($caseSensitive, $key));
    }

    public function provideIsKeyCaseSensitiveCases()
    {
        return [
            [true, true, 0],
            [true, true, 1],
            [true, [], 0],
            [true, [true], 0],
            [true, [false, true], 1],
            [true, [false, true, false], 1],
            [true, [false], 10],

            [false, false, 10],
            [false, [false], 0],
            [false, [true, false], 1],
            [false, [true, false, true], 1],
            [false, [1 => false], 1],
        ];
    }

    /**
     * @param null|string $expected
     * @param int         $id
     *
     * @dataProvider provideTokenGetNameCases
     */
    public function testTokenGetNameForId($expected, $id)
    {
        static::assertSame($expected, Token::getNameForId($id));
    }

    public function provideTokenGetNameCases()
    {
        return [
            [
                null,
                -1,
            ],
            [
                'T_CLASS',
                T_CLASS,
            ],
            [
                'CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE',
                CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
            ],
        ];
    }

    private function getBraceToken()
    {
        return new Token($this->getBraceTokenPrototype());
    }

    private function getBraceTokenPrototype()
    {
        return '(';
    }

    private function getForeachToken()
    {
        return new Token($this->getForeachTokenPrototype());
    }

    private function getForeachTokenPrototype()
    {
        static $prototype = [T_FOREACH, 'foreach'];

        return $prototype;
    }
}
