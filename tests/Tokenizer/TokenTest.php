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

use PhpCsFixer\Tokenizer\Token;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Token
 */
final class TokenTest extends TestCase
{
    public function getBraceToken()
    {
        return new Token($this->getBraceTokenPrototype());
    }

    public function getBraceTokenPrototype()
    {
        return '(';
    }

    public function getForeachToken()
    {
        return new Token($this->getForeachTokenPrototype());
    }

    public function getForeachTokenPrototype()
    {
        static $prototype = [T_FOREACH, 'foreach'];

        return $prototype;
    }

    public function testClear()
    {
        $token = $this->getForeachToken();
        $token->clear();

        $this->assertSame('', $token->getContent());
        $this->assertNull($token->getId());
        $this->assertFalse($token->isArray());
    }

    public function testGetPrototype()
    {
        $this->assertSame($this->getBraceTokenPrototype(), $this->getBraceToken()->getPrototype());
        $this->assertSame($this->getForeachTokenPrototype(), $this->getForeachToken()->getPrototype());
    }

    public function testIsArray()
    {
        $this->assertFalse($this->getBraceToken()->isArray());
        $this->assertTrue($this->getForeachToken()->isArray());
    }

    /**
     * @param Token $token
     * @param bool  $isCast
     *
     * @dataProvider provideIsCastCases
     */
    public function testIsCast(Token $token, $isCast)
    {
        $this->assertSame($isCast, $token->isCast());
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
     * @param Token $token
     * @param bool  $isClassy
     *
     * @dataProvider provideIsClassyCases
     */
    public function testIsClassy(Token $token, $isClassy)
    {
        $this->assertSame($isClassy, $token->isClassy());
    }

    public function provideIsClassyCases()
    {
        $cases = [
            [$this->getBraceToken(), false],
            [$this->getForeachToken(), false],
            [new Token([T_CLASS, 'class', 1]), true],
            [new Token([T_INTERFACE, 'interface', 1]), true],
            [new Token([T_TRAIT, 'trait', 1]), true],
        ];

        return $cases;
    }

    /**
     * @param Token $token
     * @param bool  $isComment
     *
     * @dataProvider provideIsCommentCases
     */
    public function testIsComment(Token $token, $isComment)
    {
        $this->assertSame($isComment, $token->isComment());
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

    public function testIsEmpty()
    {
        $braceToken = $this->getBraceToken();
        $this->assertFalse($braceToken->isEmpty());

        $emptyToken = new Token('');
        $this->assertTrue($emptyToken->isEmpty());

        $whitespaceToken = new Token([T_WHITESPACE, ' ']);
        $this->assertFalse($whitespaceToken->isEmpty());
    }

    public function testIsGivenKind()
    {
        $braceToken = $this->getBraceToken();
        $foreachToken = $this->getForeachToken();

        $this->assertFalse($braceToken->isGivenKind(T_FOR));
        $this->assertFalse($braceToken->isGivenKind(T_FOREACH));
        $this->assertFalse($braceToken->isGivenKind([T_FOR]));
        $this->assertFalse($braceToken->isGivenKind([T_FOREACH]));
        $this->assertFalse($braceToken->isGivenKind([T_FOR, T_FOREACH]));

        $this->assertFalse($foreachToken->isGivenKind(T_FOR));
        $this->assertTrue($foreachToken->isGivenKind(T_FOREACH));
        $this->assertFalse($foreachToken->isGivenKind([T_FOR]));
        $this->assertTrue($foreachToken->isGivenKind([T_FOREACH]));
        $this->assertTrue($foreachToken->isGivenKind([T_FOR, T_FOREACH]));
    }

    public function testIsKeywords()
    {
        $this->assertTrue($this->getForeachToken()->isKeyword());
        $this->assertFalse($this->getBraceToken()->isKeyword());
    }

    /**
     * @param int    $tokenId
     * @param string $content
     * @param bool   $isConstant
     *
     * @dataProvider provideMagicConstantCases
     */
    public function testIsMagicConstant($tokenId, $content, $isConstant = true)
    {
        $token = new Token([$tokenId, $content]);
        $this->assertSame($isConstant, $token->isMagicConstant());
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
     * @param Token $token
     * @param bool  $isNativeConstant
     *
     * @dataProvider provideIsNativeConstantCases
     */
    public function testIsNativeConstant(Token $token, $isNativeConstant)
    {
        $this->assertSame($isNativeConstant, $token->isNativeConstant());
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
     * @param Token       $token
     * @param bool        $isWhitespace
     * @param null|string $whitespaces
     *
     * @dataProvider provideIsWhitespaceCases
     */
    public function testIsWhitespace(Token $token, $isWhitespace, $whitespaces = null)
    {
        if (null !== $whitespaces) {
            $this->assertSame($isWhitespace, $token->isWhitespace($whitespaces));
        } else {
            $this->assertSame($isWhitespace, $token->isWhitespace());
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
        $this->setExpectedException($expectedExceptionClass);

        $token = new Token($prototype);
        $this->assertSame($expectedId, $token->getId());
        $this->assertSame($expectedContent, $token->getContent());
        $this->assertSame($expectedIsArray, $token->isArray());
    }

    public function provideCreatingTokenCases()
    {
        return [
            [[T_FOREACH, 'foreach'], T_FOREACH, 'foreach', true],
            ['(', null, '(', false],
            [123, null, null, null, 'InvalidArgumentException'],
            [false, null, null, null, 'InvalidArgumentException'],
            [null, null, null, null, 'InvalidArgumentException'],
        ];
    }

    public function testEqualsDefaultIsCaseSensitive()
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        $this->assertTrue($token->equals([T_FUNCTION, 'function']));
        $this->assertFalse($token->equals([T_FUNCTION, 'Function']));
    }

    /**
     * @param Token              $token
     * @param string             $equals
     * @param Token|array|string $other
     * @param bool               $caseSensitive
     *
     * @dataProvider provideEquals
     */
    public function testEquals(Token $token, $equals, $other, $caseSensitive = true)
    {
        $this->assertSame($equals, $token->equals($other, $caseSensitive));
    }

    public function provideEquals()
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

        $this->assertTrue($token->equalsAny([[T_FUNCTION, 'function']]));
        $this->assertFalse($token->equalsAny([[T_FUNCTION, 'Function']]));
    }

    /**
     * @param bool  $equalsAny
     * @param array $other
     * @param bool  $caseSensitive
     *
     * @dataProvider provideEqualsAny
     */
    public function testEqualsAny($equalsAny, array $other, $caseSensitive = true)
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        $this->assertSame($equalsAny, $token->equalsAny($other, $caseSensitive));
    }

    public function provideEqualsAny()
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
     * @param bool|array $caseSensitive
     * @param int        $key
     *
     * @dataProvider provideIsKeyCaseSensitive
     */
    public function testIsKeyCaseSensitive($isKeyCaseSensitive, $caseSensitive, $key)
    {
        $this->assertSame($isKeyCaseSensitive, Token::isKeyCaseSensitive($caseSensitive, $key));
    }

    public function provideIsKeyCaseSensitive()
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
}
