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
    public function testConstructorValidation($input): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Token($input);
    }

    public static function provideConstructorValidationCases(): iterable
    {
        yield [null];

        yield [123];

        yield [new \stdClass()];

        yield [['asd', 'asd']];

        yield [[null, 'asd']];

        yield [[new \stdClass(), 'asd']];

        yield [[T_WHITESPACE, null]];

        yield [[T_WHITESPACE, 123]];

        yield [[T_WHITESPACE, '']];

        yield [[T_WHITESPACE, new \stdClass()]];
    }

    public function testGetPrototype(): void
    {
        self::assertSame(self::getBraceTokenPrototype(), self::getBraceToken()->getPrototype());
        self::assertSame(self::getForeachTokenPrototype(), self::getForeachToken()->getPrototype());
    }

    public function testIsArray(): void
    {
        self::assertFalse(self::getBraceToken()->isArray());
        self::assertTrue(self::getForeachToken()->isArray());
    }

    /**
     * @dataProvider provideIsCastCases
     */
    public function testIsCast(Token $token, bool $isCast): void
    {
        self::assertSame($isCast, $token->isCast());
    }

    public static function provideIsCastCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([T_ARRAY_CAST, '(array)', 1]), true];

        yield [new Token([T_BOOL_CAST, '(bool)', 1]), true];

        yield [new Token([T_DOUBLE_CAST, '(double)', 1]), true];

        yield [new Token([T_INT_CAST, '(int)', 1]), true];

        yield [new Token([T_OBJECT_CAST, '(object)', 1]), true];

        yield [new Token([T_STRING_CAST, '(string)', 1]), true];

        yield [new Token([T_UNSET_CAST, '(unset)', 1]), true];
    }

    /**
     * @dataProvider provideIsClassyCases
     */
    public function testIsClassy(Token $token, bool $isClassy): void
    {
        self::assertSame($isClassy, $token->isClassy());
    }

    public static function provideIsClassyCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([T_CLASS, 'class', 1]), true];

        yield [new Token([T_INTERFACE, 'interface', 1]), true];

        yield [new Token([T_TRAIT, 'trait', 1]), true];
    }

    /**
     * @requires PHP 8.1
     */
    public function testEnumIsClassy(): void
    {
        $enumToken = new Token([T_ENUM, 'enum', 1]);

        self::assertTrue($enumToken->isClassy());
    }

    /**
     * @dataProvider provideIsCommentCases
     */
    public function testIsComment(Token $token, bool $isComment): void
    {
        self::assertSame($isComment, $token->isComment());
    }

    public static function provideIsCommentCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([T_COMMENT, '/* comment */', 1]), true];

        yield [new Token([T_DOC_COMMENT, '/** docs */', 1]), true];

        // @TODO: drop condition when PHP 8.0+ is required
        if (\defined('T_ATTRIBUTE')) {
            yield [new Token([T_ATTRIBUTE, '#[', 1]), false];
        }
    }

    /**
     * @dataProvider provideIsObjectOperatorCases
     */
    public function testIsObjectOperator(Token $token, bool $isObjectOperator): void
    {
        self::assertSame($isObjectOperator, $token->isObjectOperator());
    }

    public static function provideIsObjectOperatorCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([T_COMMENT, '/* comment */']), false];

        yield [new Token([T_DOUBLE_COLON, '::']), false];

        yield [new Token([T_OBJECT_OPERATOR, '->']), true];

        if (\defined('T_NULLSAFE_OBJECT_OPERATOR')) {
            yield [new Token([T_NULLSAFE_OBJECT_OPERATOR, '?->']), true];
        }
    }

    public function testIsGivenKind(): void
    {
        $braceToken = self::getBraceToken();
        $foreachToken = self::getForeachToken();

        self::assertFalse($braceToken->isGivenKind(T_FOR));
        self::assertFalse($braceToken->isGivenKind(T_FOREACH));
        self::assertFalse($braceToken->isGivenKind([T_FOR]));
        self::assertFalse($braceToken->isGivenKind([T_FOREACH]));
        self::assertFalse($braceToken->isGivenKind([T_FOR, T_FOREACH]));

        self::assertFalse($foreachToken->isGivenKind(T_FOR));
        self::assertTrue($foreachToken->isGivenKind(T_FOREACH));
        self::assertFalse($foreachToken->isGivenKind([T_FOR]));
        self::assertTrue($foreachToken->isGivenKind([T_FOREACH]));
        self::assertTrue($foreachToken->isGivenKind([T_FOR, T_FOREACH]));
    }

    public function testIsKeywords(): void
    {
        self::assertTrue(self::getForeachToken()->isKeyword());
        self::assertFalse(self::getBraceToken()->isKeyword());
    }

    /**
     * @param ?int $tokenId
     *
     * @dataProvider provideIsMagicConstantCases
     */
    public function testIsMagicConstant(?int $tokenId, string $content, bool $isConstant = true): void
    {
        $token = new Token(
            null === $tokenId ? $content : [$tokenId, $content]
        );

        self::assertSame($isConstant, $token->isMagicConstant());
    }

    public static function provideIsMagicConstantCases(): iterable
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
            yield [$case[0], strtolower($case[1])];
        }

        foreach ([self::getForeachToken(), self::getBraceToken()] as $token) {
            yield [$token->getId(), $token->getContent(), false];

            yield [$token->getId(), strtolower($token->getContent()), false];
        }
    }

    /**
     * @dataProvider provideIsNativeConstantCases
     */
    public function testIsNativeConstant(Token $token, bool $isNativeConstant): void
    {
        self::assertSame($isNativeConstant, $token->isNativeConstant());
    }

    public static function provideIsNativeConstantCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([T_STRING, 'null', 1]), true];

        yield [new Token([T_STRING, 'false', 1]), true];

        yield [new Token([T_STRING, 'true', 1]), true];

        yield [new Token([T_STRING, 'tRuE', 1]), true];

        yield [new Token([T_STRING, 'TRUE', 1]), true];
    }

    /**
     * @dataProvider provideIsWhitespaceCases
     */
    public function testIsWhitespace(Token $token, bool $isWhitespace, ?string $whitespaces = null): void
    {
        if (null !== $whitespaces) {
            self::assertSame($isWhitespace, $token->isWhitespace($whitespaces));
        } else {
            self::assertSame($isWhitespace, $token->isWhitespace(null));
            self::assertSame($isWhitespace, $token->isWhitespace());
        }
    }

    public static function provideIsWhitespaceCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token(' '), true];

        yield [new Token("\t "), true];

        yield [new Token("\t "), false, ' '];

        yield [new Token([T_WHITESPACE, "\r", 1]), true];

        yield [new Token([T_WHITESPACE, "\0", 1]), true];

        yield [new Token([T_WHITESPACE, "\x0B", 1]), true];

        yield [new Token([T_WHITESPACE, "\n", 1]), true];

        yield [new Token([T_WHITESPACE, "\n", 1]), false, " \t"];
    }

    /**
     * @param mixed                     $prototype
     * @param ?class-string<\Throwable> $expectedExceptionClass
     *
     * @dataProvider provideCreatingTokenCases
     */
    public function testCreatingToken($prototype, ?int $expectedId, ?string $expectedContent, ?bool $expectedIsArray, ?string $expectedExceptionClass = null): void
    {
        if (null !== $expectedExceptionClass) {
            $this->expectException($expectedExceptionClass);
        }

        $token = new Token($prototype);
        self::assertSame($expectedId, $token->getId());
        self::assertSame($expectedContent, $token->getContent());
        self::assertSame($expectedIsArray, $token->isArray());
    }

    public static function provideCreatingTokenCases(): iterable
    {
        yield [[T_FOREACH, 'foreach'], T_FOREACH, 'foreach', true];

        yield ['(', null, '(', false];

        yield [123, null, null, null, \InvalidArgumentException::class];

        yield [false, null, null, null, \InvalidArgumentException::class];

        yield [null, null, null, null, \InvalidArgumentException::class];
    }

    public function testEqualsDefaultIsCaseSensitive(): void
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        self::assertTrue($token->equals([T_FUNCTION, 'function']));
        self::assertFalse($token->equals([T_FUNCTION, 'Function']));
    }

    /**
     * @param array{0: int, 1?: string}|string|Token $other
     *
     * @dataProvider provideEqualsCases
     */
    public function testEquals(Token $token, bool $equals, $other, bool $caseSensitive = true): void
    {
        self::assertSame($equals, $token->equals($other, $caseSensitive));
    }

    public static function provideEqualsCases(): iterable
    {
        $brace = self::getBraceToken();
        $function = new Token([T_FUNCTION, 'function', 1]);

        yield [$brace, false, '!'];

        yield [$brace, false, '!', false];

        yield [$brace, true, '('];

        yield [$brace, true, '(', false];

        yield [$function, false, '('];

        yield [$function, false, '(', false];

        yield [$function, false, [T_NAMESPACE]];

        yield [$function, false, [T_NAMESPACE], false];

        yield [$function, false, [T_VARIABLE, 'function']];

        yield [$function, false, [T_VARIABLE, 'function'], false];

        yield [$function, false, [T_VARIABLE, 'Function']];

        yield [$function, false, [T_VARIABLE, 'Function'], false];

        yield [$function, true, [T_FUNCTION]];

        yield [$function, true, [T_FUNCTION], false];

        yield [$function, true, [T_FUNCTION, 'function']];

        yield [$function, true, [T_FUNCTION, 'function'], false];

        yield [$function, false, [T_FUNCTION, 'Function']];

        yield [$function, true, [T_FUNCTION, 'Function'], false];

        yield [$function, false, [T_FUNCTION, 'junction'], false];

        yield [$function, true, new Token([T_FUNCTION, 'function'])];

        yield [$function, false, new Token([T_FUNCTION, 'Function'])];

        yield [$function, true, new Token([T_FUNCTION, 'Function']), false];

        // if it is an array any additional field is checked too
        yield [$function, false, [T_FUNCTION, 'function', 'unexpected']];

        yield [new Token('&'), true, '&'];
        if (\defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG')) { // @TODO: drop condition when PHP 8.1+ is required
            yield [new Token('&'), true, new Token([T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, '&'])];

            yield [new Token('&'), true, new Token([T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&'])];

            yield [new Token([T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, '&']), true, '&'];

            yield [new Token([T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, '&']), true, new Token([T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, '&'])];

            yield [new Token([T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&']), true, '&'];

            yield [new Token([T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&']), true, new Token([T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&'])];
        }
    }

    public function testEqualsAnyDefaultIsCaseSensitive(): void
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        self::assertTrue($token->equalsAny([[T_FUNCTION, 'function']]));
        self::assertFalse($token->equalsAny([[T_FUNCTION, 'Function']]));
    }

    /**
     * @param list<array{0: int, 1?: string}|string|Token> $other
     *
     * @dataProvider provideEqualsAnyCases
     */
    public function testEqualsAny(bool $equalsAny, array $other, bool $caseSensitive = true): void
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        self::assertSame($equalsAny, $token->equalsAny($other, $caseSensitive));
    }

    public static function provideEqualsAnyCases(): iterable
    {
        $brace = self::getBraceToken();
        $foreach = self::getForeachToken();

        yield [false, []];

        yield [false, [$brace]];

        yield [false, [$brace, $foreach]];

        yield [true, [$brace, $foreach, [T_FUNCTION]]];

        yield [true, [$brace, $foreach, [T_FUNCTION, 'function']]];

        yield [false, [$brace, $foreach, [T_FUNCTION, 'Function']]];

        yield [true, [$brace, $foreach, [T_FUNCTION, 'Function']], false];

        yield [false, [[T_VARIABLE, 'junction'], [T_FUNCTION, 'junction']], false];
    }

    /**
     * @param bool|list<bool> $caseSensitive
     *
     * @dataProvider provideIsKeyCaseSensitiveCases
     *
     * @group legacy
     */
    public function testIsKeyCaseSensitive(bool $isKeyCaseSensitive, $caseSensitive, int $key): void
    {
        $this->expectDeprecation('Method "PhpCsFixer\Tokenizer\Token::isKeyCaseSensitive" is deprecated and will be removed in the next major version.');
        self::assertSame($isKeyCaseSensitive, Token::isKeyCaseSensitive($caseSensitive, $key));
    }

    public static function provideIsKeyCaseSensitiveCases(): iterable
    {
        yield [true, true, 0];

        yield [true, true, 1];

        yield [true, [], 0];

        yield [true, [true], 0];

        yield [true, [false, true], 1];

        yield [true, [false, true, false], 1];

        yield [true, [false], 10];

        yield [false, false, 10];

        yield [false, [false], 0];

        yield [false, [true, false], 1];

        yield [false, [true, false, true], 1];

        yield [false, [1 => false], 1];
    }

    /**
     * @dataProvider provideTokenGetNameForIdCases
     */
    public function testTokenGetNameForId(?string $expected, int $id): void
    {
        self::assertSame($expected, Token::getNameForId($id));
    }

    public static function provideTokenGetNameForIdCases(): iterable
    {
        yield [
            null,
            -1,
        ];

        yield [
            'T_CLASS',
            T_CLASS,
        ];

        yield [
            'CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE',
            CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
        ];
    }

    /**
     * @dataProvider provideGetNameCases
     */
    public function testGetName(Token $token, ?string $expected = null): void
    {
        self::assertSame($expected, $token->getName());
    }

    public static function provideGetNameCases(): iterable
    {
        yield [
            new Token([T_FUNCTION, 'function', 1]),
            'T_FUNCTION',
        ];

        yield [
            new Token(')'),
            null,
        ];

        yield [
            new Token(''),
            null,
        ];
    }

    /**
     * @param array<string, mixed> $expected
     *
     * @dataProvider provideToArrayCases
     */
    public function testToArray(Token $token, array $expected): void
    {
        self::assertSame($expected, $token->toArray());
    }

    public static function provideToArrayCases(): iterable
    {
        yield [
            new Token([T_FUNCTION, 'function', 1]),
            [
                'id' => T_FUNCTION,
                'name' => 'T_FUNCTION',
                'content' => 'function',
                'isArray' => true,
                'changed' => false,
            ],
        ];

        yield [
            new Token(')'),
            [
                'id' => null,
                'name' => null,
                'content' => ')',
                'isArray' => false,
                'changed' => false,
            ],
        ];

        yield [
            new Token(''),
            [
                'id' => null,
                'name' => null,
                'content' => '',
                'isArray' => false,
                'changed' => false,
            ],
        ];
    }

    public function testGetClassyTokenKinds(): void
    {
        if (\defined('T_ENUM')) {
            self::assertSame([T_CLASS, T_TRAIT, T_INTERFACE, T_ENUM], Token::getClassyTokenKinds());
        } else {
            self::assertSame([T_CLASS, T_TRAIT, T_INTERFACE], Token::getClassyTokenKinds());
        }
    }

    private static function getBraceToken(): Token
    {
        return new Token(self::getBraceTokenPrototype());
    }

    private static function getBraceTokenPrototype(): string
    {
        return '(';
    }

    private static function getForeachToken(): Token
    {
        return new Token(self::getForeachTokenPrototype());
    }

    /**
     * @return array{int, string}
     */
    private static function getForeachTokenPrototype(): array
    {
        static $prototype = [T_FOREACH, 'foreach'];

        return $prototype;
    }
}
