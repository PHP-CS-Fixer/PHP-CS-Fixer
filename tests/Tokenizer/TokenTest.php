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
        static $prototype = array(T_FOREACH, 'foreach');

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
        return array(
            array($this->getBraceToken(), false),
            array($this->getForeachToken(), false),
            array(new Token(array(T_ARRAY_CAST, '(array)', 1)), true),
            array(new Token(array(T_BOOL_CAST, '(bool)', 1)), true),
            array(new Token(array(T_DOUBLE_CAST, '(double)', 1)), true),
            array(new Token(array(T_INT_CAST, '(int)', 1)), true),
            array(new Token(array(T_OBJECT_CAST, '(object)', 1)), true),
            array(new Token(array(T_STRING_CAST, '(string)', 1)), true),
            array(new Token(array(T_UNSET_CAST, '(unset)', 1)), true),
        );
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
        $cases = array(
            array($this->getBraceToken(), false),
            array($this->getForeachToken(), false),
            array(new Token(array(T_CLASS, 'class', 1)), true),
            array(new Token(array(T_INTERFACE, 'interface', 1)), true),
        );

        if (defined('T_TRAIT')) {
            $cases[] = array(new Token(array(T_TRAIT, 'trait', 1)), true);
        }

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
        return array(
            array($this->getBraceToken(), false),
            array($this->getForeachToken(), false),
            array(new Token(array(T_COMMENT, '/* comment */', 1)), true),
            array(new Token(array(T_DOC_COMMENT, '/** docs */', 1)), true),
        );
    }

    public function testIsEmpty()
    {
        $braceToken = $this->getBraceToken();
        $this->assertFalse($braceToken->isEmpty());

        $emptyToken = new Token('');
        $this->assertTrue($emptyToken->isEmpty());

        $whitespaceToken = new Token(array(T_WHITESPACE, ' '));
        $this->assertFalse($whitespaceToken->isEmpty());
    }

    public function testIsGivenKind()
    {
        $braceToken = $this->getBraceToken();
        $foreachToken = $this->getForeachToken();

        $this->assertFalse($braceToken->isGivenKind(T_FOR));
        $this->assertFalse($braceToken->isGivenKind(T_FOREACH));
        $this->assertFalse($braceToken->isGivenKind(array(T_FOR)));
        $this->assertFalse($braceToken->isGivenKind(array(T_FOREACH)));
        $this->assertFalse($braceToken->isGivenKind(array(T_FOR, T_FOREACH)));

        $this->assertFalse($foreachToken->isGivenKind(T_FOR));
        $this->assertTrue($foreachToken->isGivenKind(T_FOREACH));
        $this->assertFalse($foreachToken->isGivenKind(array(T_FOR)));
        $this->assertTrue($foreachToken->isGivenKind(array(T_FOREACH)));
        $this->assertTrue($foreachToken->isGivenKind(array(T_FOR, T_FOREACH)));
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
        $token = new Token(array($tokenId, $content));
        $this->assertSame($isConstant, $token->isMagicConstant());
    }

    public function provideMagicConstantCases()
    {
        $cases = array(
            array(T_CLASS_C, '__CLASS__'),
            array(T_DIR, '__DIR__'),
            array(T_FILE, '__FILE__'),
            array(T_FUNC_C, '__FUNCTION__'),
            array(T_LINE, '__LINE__'),
            array(T_METHOD_C, '__METHOD__'),
            array(T_NS_C, '__NAMESPACE__'),
        );

        if (defined('T_TRAIT_C')) {
            $cases[] = array(T_TRAIT_C, '__TRAIT__');
        }

        foreach ($cases as $case) {
            $cases[] = array($case[0], strtolower($case[1]));
        }

        foreach (array($this->getForeachToken(), $this->getBraceToken()) as $token) {
            $cases[] = array($token->getId(), $token->getContent(), false);
            $cases[] = array($token->getId(), strtolower($token->getContent()), false);
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
        return array(
            array($this->getBraceToken(), false),
            array($this->getForeachToken(), false),
            array(new Token(array(T_STRING, 'null', 1)), true),
            array(new Token(array(T_STRING, 'false', 1)), true),
            array(new Token(array(T_STRING, 'true', 1)), true),
            array(new Token(array(T_STRING, 'tRuE', 1)), true),
            array(new Token(array(T_STRING, 'TRUE', 1)), true),
        );
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
        return array(
            array($this->getBraceToken(), false),
            array($this->getForeachToken(), false),
            array(new Token(' '), true),
            array(new Token("\t "), true),
            array(new Token("\t "), false, ' '),
            array(new Token(array(T_WHITESPACE, "\r", 1)), true),
            array(new Token(array(T_WHITESPACE, "\0", 1)), true),
            array(new Token(array(T_WHITESPACE, "\x0B", 1)), true),
            array(new Token(array(T_WHITESPACE, "\n", 1)), true),
            array(new Token(array(T_WHITESPACE, "\n", 1)), false, " \t"),
        );
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
        return array(
            array(array(T_FOREACH, 'foreach'), T_FOREACH, 'foreach', true),
            array('(', null, '(', false),
            array(123, null, null, null, 'InvalidArgumentException'),
            array(false, null, null, null, 'InvalidArgumentException'),
            array(null, null, null, null, 'InvalidArgumentException'),
        );
    }

    public function testEqualsDefaultIsCaseSensitive()
    {
        $token = new Token(array(T_FUNCTION, 'function', 1));

        $this->assertTrue($token->equals(array(T_FUNCTION, 'function')));
        $this->assertFalse($token->equals(array(T_FUNCTION, 'Function')));
    }

    /**
     * @param Token              $token
     * @param string             $equals
     * @param Token|array|string $other
     * @param bool               $caseSensitive
     *
     * @dataProvider provideEqualsCases
     */
    public function testEquals(Token $token, $equals, $other, $caseSensitive = true)
    {
        $this->assertSame($equals, $token->equals($other, $caseSensitive));
    }

    public function provideEqualsCases()
    {
        $brace = $this->getBraceToken();
        $function = new Token(array(T_FUNCTION, 'function', 1));

        return array(
            array($brace, false, '!'),
            array($brace, false, '!', false),
            array($brace, true, '('),
            array($brace, true, '(', false),
            array($function, false, '('),
            array($function, false, '(', false),

            array($function, false, array(T_NAMESPACE)),
            array($function, false, array(T_NAMESPACE), false),
            array($function, false, array(T_VARIABLE, 'function')),
            array($function, false, array(T_VARIABLE, 'function'), false),
            array($function, false, array(T_VARIABLE, 'Function')),
            array($function, false, array(T_VARIABLE, 'Function'), false),
            array($function, true, array(T_FUNCTION)),
            array($function, true, array(T_FUNCTION), false),
            array($function, true, array(T_FUNCTION, 'function')),
            array($function, true, array(T_FUNCTION, 'function'), false),
            array($function, false, array(T_FUNCTION, 'Function')),
            array($function, true, array(T_FUNCTION, 'Function'), false),
            array($function, false, array(T_FUNCTION, 'junction'), false),

            array($function, true, new Token(array(T_FUNCTION, 'function'))),
            array($function, false, new Token(array(T_FUNCTION, 'Function'))),
            array($function, true, new Token(array(T_FUNCTION, 'Function')), false),

            // if it is an array any additional field is checked too
            array($function, false, array(T_FUNCTION, 'function', 'unexpected')),
        );
    }

    public function testEqualsAnyDefaultIsCaseSensitive()
    {
        $token = new Token(array(T_FUNCTION, 'function', 1));

        $this->assertTrue($token->equalsAny(array(array(T_FUNCTION, 'function'))));
        $this->assertFalse($token->equalsAny(array(array(T_FUNCTION, 'Function'))));
    }

    /**
     * @param bool  $equalsAny
     * @param array $other
     * @param bool  $caseSensitive
     *
     * @dataProvider provideEqualsAnyCases
     */
    public function testEqualsAny($equalsAny, array $other, $caseSensitive = true)
    {
        $token = new Token(array(T_FUNCTION, 'function', 1));

        $this->assertSame($equalsAny, $token->equalsAny($other, $caseSensitive));
    }

    public function provideEqualsAnyCases()
    {
        $brace = $this->getBraceToken();
        $foreach = $this->getForeachToken();

        return array(
            array(false, array()),
            array(false, array($brace)),
            array(false, array($brace, $foreach)),
            array(true, array($brace, $foreach, array(T_FUNCTION))),
            array(true, array($brace, $foreach, array(T_FUNCTION, 'function'))),
            array(false, array($brace, $foreach, array(T_FUNCTION, 'Function'))),
            array(true, array($brace, $foreach, array(T_FUNCTION, 'Function')), false),
            array(false, array(array(T_VARIABLE, 'junction'), array(T_FUNCTION, 'junction')), false),
        );
    }

    /**
     * @param bool       $isKeyCaseSensitive
     * @param bool|array $caseSensitive
     * @param int        $key
     *
     * @dataProvider provideIsKeyCaseSensitiveCases
     */
    public function testIsKeyCaseSensitive($isKeyCaseSensitive, $caseSensitive, $key)
    {
        $this->assertSame($isKeyCaseSensitive, Token::isKeyCaseSensitive($caseSensitive, $key));
    }

    public function provideIsKeyCaseSensitiveCases()
    {
        return array(
            array(true, true, 0),
            array(true, true, 1),
            array(true, array(), 0),
            array(true, array(true), 0),
            array(true, array(false, true), 1),
            array(true, array(false, true, false), 1),
            array(true, array(false), 10),

            array(false, false, 10),
            array(false, array(false), 0),
            array(false, array(true, false), 1),
            array(false, array(true, false, true), 1),
            array(false, array(1 => false), 1),
        );
    }
}
