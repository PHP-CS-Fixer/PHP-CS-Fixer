<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer;

use Symfony\CS\Tokenizer\Token;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TokenTest extends \PHPUnit_Framework_TestCase
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
     * @dataProvider provideIsCastCases
     */
    public function testIsCast($token, $isCast)
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
     * @dataProvider provideIsClassyCases
     */
    public function testIsClassy($token, $isClassy)
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
     * @dataProvider provideIsCommentCases
     */
    public function testIsComment($token, $isComment)
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

        $braceToken->setContent('');
        $this->assertTrue($braceToken->isEmpty());

        $whitespaceToken = new Token(array(T_WHITESPACE, ' '));
        $this->assertFalse($whitespaceToken->isEmpty());

        $whitespaceToken->setContent('');
        $this->assertTrue($whitespaceToken->isEmpty());

        $whitespaceToken->override(array(null, ''));
        $this->assertTrue($whitespaceToken->isEmpty());

        $whitespaceToken = new Token(array(T_WHITESPACE, ' '));
        $whitespaceToken->clear();
        $this->assertTrue($whitespaceToken->isEmpty());
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
     * @dataProvider provideIsNativeConstantCases
     */
    public function testIsNativeConstant($token, $isNativeConstant)
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
     * @dataProvider provideIsWhitespaceCases
     */
    public function testIsWhitespace($token, $isWhitespace, $whitespaces = null)
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

    public function testPropertiesOfArrayToken()
    {
        $prototype = $this->getForeachTokenPrototype();
        $token = $this->getForeachToken();

        $this->assertSame($prototype[0], $token->getId());
        $this->assertSame($prototype[1], $token->getContent());
        $this->assertTrue($token->isArray());
    }

    public function testPropertiesOfNonArrayToken()
    {
        $prototype = $this->getBraceTokenPrototype();
        $token = $this->getBraceToken();

        $this->assertSame($prototype, $token->getContent());
        $this->assertNull($token->getId());
        $this->assertFalse($token->isArray());
    }

    public function testEqualsDefaultIsCaseSensitive()
    {
        $token = new Token(array(T_FUNCTION, 'function', 1));

        $this->assertTrue($token->equals(array(T_FUNCTION, 'function')));
        $this->assertFalse($token->equals(array(T_FUNCTION, 'Function')));
    }

    /**
     * @dataProvider provideEquals
     */
    public function testEquals(Token $token, $equals, $other, $caseSensitive = true)
    {
        $this->assertSame($equals, $token->equals($other, $caseSensitive));
    }

    public function provideEquals()
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
     * @dataProvider provideEqualsAny
     */
    public function testEqualsAny($equalsAny, $other, $caseSensitive = true)
    {
        $token = new Token(array(T_FUNCTION, 'function', 1));

        $this->assertSame($equalsAny, $token->equalsAny($other, $caseSensitive));
    }

    public function provideEqualsAny()
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
            array(false, array($brace, $foreach, array(T_FUNCTION, 'Function')), array()),
            array(false, array($brace, $foreach, array(T_FUNCTION, 'Function')), array(false)),
            array(false, array($brace, $foreach, array(T_FUNCTION, 'Function')), array(false, false)),
            array(true, array($brace, $foreach, array(T_FUNCTION, 'Function')), array(false, false, false)),

            array(true, array(array(T_FUNCTION, 'Function'), array(T_FUNCTION, 'function')), array(true, true)),
            array(true, array(array(T_FUNCTION, 'Function'), array(T_FUNCTION, 'function')), array(true)),
            array(true, array(array(T_FUNCTION, 'Function'), array(T_FUNCTION, 'function')), array(false, false)),
            array(true, array(array(T_FUNCTION, 'Function'), array(T_FUNCTION, 'function')), array(false)),

            array(false, array(array(T_FUNCTION, 'Function'), array(T_VARIABLE, 'function')), array(true, false)),
            array(false, array(array(T_FUNCTION, 'Function'), array(T_VARIABLE, 'function')), array(true, true)),

            array(true, array(',', '}', array(T_FUNCTION, 'Function')), array(2 => false)),

            array(false, array(array(T_VARIABLE, 'junction'), array(T_FUNCTION, 'junction')), false),
        );
    }

    /**
     * @dataProvider provideIsKeyCaseSensitive
     */
    public function testIsKeyCaseSensitive($isKeyCaseSensitive, $caseSensitive, $key)
    {
        $this->assertSame($isKeyCaseSensitive, Token::isKeyCaseSensitive($caseSensitive, $key));
    }

    public function provideIsKeyCaseSensitive()
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
