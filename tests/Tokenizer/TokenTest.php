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
use PhpCsFixer\Tokenizer\FCT;
use PhpCsFixer\Tokenizer\Token;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @phpstan-import-type _PhpTokenPrototypePartial from Token
 *
 * @covers \PhpCsFixer\Tokenizer\Token
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
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

    /**
     * @return iterable<int, array{mixed}>
     */
    public static function provideConstructorValidationCases(): iterable
    {
        yield [null];

        yield [123];

        yield [new \stdClass()];

        yield [['asd', 'asd']];

        yield [[null, 'asd']];

        yield [[new \stdClass(), 'asd']];

        yield [[\T_WHITESPACE, null]];

        yield [[\T_WHITESPACE, 123]];

        yield [[\T_WHITESPACE, '']];

        yield [[\T_WHITESPACE, new \stdClass()]];
    }

    public function testGetPrototype(): void
    {
        self::assertSame('(', self::getBraceToken()->getPrototype());
        self::assertSame([\T_FOREACH, 'foreach'], self::getForeachToken()->getPrototype());
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

    /**
     * @return iterable<int, array{Token, bool}>
     */
    public static function provideIsCastCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([\T_ARRAY_CAST, '(array)', 1]), true];

        yield [new Token([\T_BOOL_CAST, '(bool)', 1]), true];

        yield [new Token([\T_DOUBLE_CAST, '(double)', 1]), true];

        yield [new Token([\T_INT_CAST, '(int)', 1]), true];

        yield [new Token([\T_OBJECT_CAST, '(object)', 1]), true];

        yield [new Token([\T_STRING_CAST, '(string)', 1]), true];

        yield [new Token([\T_UNSET_CAST, '(unset)', 1]), true];
    }

    /**
     * @dataProvider provideIsClassyCases
     */
    public function testIsClassy(Token $token, bool $isClassy): void
    {
        self::assertSame($isClassy, $token->isClassy());
    }

    /**
     * @return iterable<int, array{Token, bool}>
     */
    public static function provideIsClassyCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([\T_CLASS, 'class', 1]), true];

        yield [new Token([\T_INTERFACE, 'interface', 1]), true];

        yield [new Token([\T_TRAIT, 'trait', 1]), true];
    }

    /**
     * @requires PHP 8.1
     */
    public function testEnumIsClassy(): void
    {
        $enumToken = new Token([\T_ENUM, 'enum', 1]);

        self::assertTrue($enumToken->isClassy());
    }

    /**
     * @dataProvider provideIsCommentCases
     */
    public function testIsComment(Token $token, bool $isComment): void
    {
        self::assertSame($isComment, $token->isComment());
    }

    /**
     * @return iterable<int, array{Token, bool}>
     */
    public static function provideIsCommentCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([\T_COMMENT, '/* comment */', 1]), true];

        yield [new Token([\T_DOC_COMMENT, '/** docs */', 1]), true];
    }

    /**
     * @dataProvider provideIsComment81Cases
     *
     * @requires PHP 8.0
     */
    public function testIsComment81(Token $token, bool $isComment): void
    {
        self::assertSame($isComment, $token->isComment());
    }

    /**
     * @return iterable<int, array{Token, bool}>
     */
    public static function provideIsComment81Cases(): iterable
    {
        yield [new Token([FCT::T_ATTRIBUTE, '#[', 1]), false];
    }

    /**
     * @dataProvider provideIsObjectOperatorCases
     */
    public function testIsObjectOperator(Token $token, bool $isObjectOperator): void
    {
        self::assertSame($isObjectOperator, $token->isObjectOperator());
    }

    /**
     * @return iterable<int, array{Token, bool}>
     */
    public static function provideIsObjectOperatorCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([\T_COMMENT, '/* comment */']), false];

        yield [new Token([\T_DOUBLE_COLON, '::']), false];

        yield [new Token([\T_OBJECT_OPERATOR, '->']), true];
    }

    /**
     * @dataProvider provideIsObjectOperator80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsObjectOperator80(Token $token, bool $isObjectOperator): void
    {
        self::assertSame($isObjectOperator, $token->isObjectOperator());
    }

    /**
     * @return iterable<int, array{Token, bool}>
     */
    public static function provideIsObjectOperator80Cases(): iterable
    {
        yield [new Token([FCT::T_NULLSAFE_OBJECT_OPERATOR, '?->']), true];
    }

    public function testIsGivenKind(): void
    {
        $braceToken = self::getBraceToken();
        $foreachToken = self::getForeachToken();

        self::assertFalse($braceToken->isGivenKind(\T_FOR));
        self::assertFalse($braceToken->isGivenKind(\T_FOREACH));
        self::assertFalse($braceToken->isGivenKind([\T_FOR]));
        self::assertFalse($braceToken->isGivenKind([\T_FOREACH]));
        self::assertFalse($braceToken->isGivenKind([\T_FOR, \T_FOREACH]));

        self::assertFalse($foreachToken->isGivenKind(\T_FOR));
        self::assertTrue($foreachToken->isGivenKind(\T_FOREACH));
        self::assertFalse($foreachToken->isGivenKind([\T_FOR]));
        self::assertTrue($foreachToken->isGivenKind([\T_FOREACH]));
        self::assertTrue($foreachToken->isGivenKind([\T_FOR, \T_FOREACH]));
    }

    public function testIsKeywords(): void
    {
        self::assertTrue(self::getForeachToken()->isKeyword());
        self::assertFalse(self::getBraceToken()->isKeyword());
    }

    /**
     * @dataProvider provideIsMagicConstantCases
     */
    public function testIsMagicConstant(?int $tokenId, string $content, bool $isConstant = true): void
    {
        $token = new Token(
            null === $tokenId ? $content : [$tokenId, $content]
        );

        self::assertSame($isConstant, $token->isMagicConstant());
    }

    /**
     * @return iterable<int, array{0: null|int, 1: string, 2?: bool}>
     */
    public static function provideIsMagicConstantCases(): iterable
    {
        $cases = [
            [\T_CLASS_C, '__CLASS__'],
            [\T_DIR, '__DIR__'],
            [\T_FILE, '__FILE__'],
            [\T_FUNC_C, '__FUNCTION__'],
            [\T_LINE, '__LINE__'],
            [\T_METHOD_C, '__METHOD__'],
            [\T_NS_C, '__NAMESPACE__'],
            [\T_TRAIT_C, '__TRAIT__'],
        ];

        foreach ($cases as $case) {
            yield [$case[0], strtolower($case[1])];
        }

        $foreachToken = self::getForeachToken();

        yield [$foreachToken->getId(), $foreachToken->getContent(), false];

        yield [$foreachToken->getId(), strtoupper($foreachToken->getContent()), false];

        $braceToken = self::getBraceToken();

        yield [$braceToken->getId(), $braceToken->getContent(), false];
    }

    /**
     * @dataProvider provideIsNativeConstantCases
     */
    public function testIsNativeConstant(Token $token, bool $isNativeConstant): void
    {
        self::assertSame($isNativeConstant, $token->isNativeConstant());
    }

    /**
     * @return iterable<int, array{Token, bool}>
     */
    public static function provideIsNativeConstantCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token([\T_STRING, 'null', 1]), true];

        yield [new Token([\T_STRING, 'false', 1]), true];

        yield [new Token([\T_STRING, 'true', 1]), true];

        yield [new Token([\T_STRING, 'tRuE', 1]), true];

        yield [new Token([\T_STRING, 'TRUE', 1]), true];
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

    /**
     * @return iterable<int, array{0: Token, 1: bool, 2?: string}>
     */
    public static function provideIsWhitespaceCases(): iterable
    {
        yield [self::getBraceToken(), false];

        yield [self::getForeachToken(), false];

        yield [new Token(' '), true];

        yield [new Token("\t "), true];

        yield [new Token("\t "), false, ' '];

        yield [new Token([\T_WHITESPACE, "\r", 1]), true];

        yield [new Token([\T_WHITESPACE, "\0", 1]), true];

        yield [new Token([\T_WHITESPACE, "\x0B", 1]), true];

        yield [new Token([\T_WHITESPACE, "\n", 1]), true];

        yield [new Token([\T_WHITESPACE, "\n", 1]), false, " \t"];
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

    /**
     * @return iterable<int, array{0: mixed, 1: null|int, 2: null|string, 3: null|bool, 4?: string}>
     */
    public static function provideCreatingTokenCases(): iterable
    {
        yield [[\T_FOREACH, 'foreach'], \T_FOREACH, 'foreach', true];

        yield ['(', null, '(', false];

        yield [123, null, null, null, \InvalidArgumentException::class];

        yield [false, null, null, null, \InvalidArgumentException::class];

        yield [null, null, null, null, \InvalidArgumentException::class];
    }

    public function testEqualsDefaultIsCaseSensitive(): void
    {
        $token = new Token([\T_FUNCTION, 'function', 1]);

        self::assertTrue($token->equals([\T_FUNCTION, 'function']));
        self::assertFalse($token->equals([\T_FUNCTION, 'Function']));
    }

    /**
     * @param _PhpTokenPrototypePartial|Token $other
     *
     * @dataProvider provideEqualsCases
     */
    public function testEquals(Token $token, bool $equals, $other, bool $caseSensitive = true): void
    {
        self::assertSame($equals, $token->equals($other, $caseSensitive));
    }

    /**
     * @return iterable<int, array{0: Token, 1: bool, 2: _PhpTokenPrototypePartial|Token, 3?: bool}>
     */
    public static function provideEqualsCases(): iterable
    {
        $brace = self::getBraceToken();
        $function = new Token([\T_FUNCTION, 'function', 1]);

        yield [$brace, false, '!'];

        yield [$brace, false, '!', false];

        yield [$brace, true, '('];

        yield [$brace, true, '(', false];

        yield [$function, false, '('];

        yield [$function, false, '(', false];

        yield [$function, false, [\T_NAMESPACE]];

        yield [$function, false, [\T_NAMESPACE], false];

        yield [$function, false, [\T_VARIABLE, 'function']];

        yield [$function, false, [\T_VARIABLE, 'function'], false];

        yield [$function, false, [\T_VARIABLE, 'Function']];

        yield [$function, false, [\T_VARIABLE, 'Function'], false];

        yield [$function, true, [\T_FUNCTION]];

        yield [$function, true, [\T_FUNCTION], false];

        yield [$function, true, [\T_FUNCTION, 'function']];

        yield [$function, true, [\T_FUNCTION, 'function'], false];

        yield [$function, false, [\T_FUNCTION, 'Function']];

        yield [$function, true, [\T_FUNCTION, 'Function'], false];

        yield [$function, false, [\T_FUNCTION, 'junction'], false];

        yield [$function, true, new Token([\T_FUNCTION, 'function'])];

        yield [$function, false, new Token([\T_FUNCTION, 'Function'])];

        yield [$function, true, new Token([\T_FUNCTION, 'Function']), false];

        // if it is an array any additional field is checked too
        yield [$function, false, [\T_FUNCTION, 'function', 'unexpected']];

        yield [new Token('&'), true, '&'];
    }

    /**
     * @param _PhpTokenPrototypePartial|Token $other
     *
     * @dataProvider provideEquals81Cases
     *
     * @requires PHP 8.1
     */
    public function testEquals81(Token $token, bool $equals, $other): void
    {
        self::assertSame($equals, $token->equals($other));
    }

    /**
     * @return iterable<int, array{0: Token, 1: bool, 2: _PhpTokenPrototypePartial|Token}>
     */
    public static function provideEquals81Cases(): iterable
    {
        yield [new Token('&'), true, new Token([FCT::T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, '&'])];

        yield [new Token('&'), true, new Token([FCT::T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&'])];

        yield [new Token([FCT::T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, '&']), true, '&'];

        yield [new Token([FCT::T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, '&']), true, new Token([FCT::T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, '&'])];

        yield [new Token([FCT::T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&']), true, '&'];

        yield [new Token([FCT::T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&']), true, new Token([FCT::T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&'])];
    }

    public function testEqualsAnyDefaultIsCaseSensitive(): void
    {
        $token = new Token([\T_FUNCTION, 'function', 1]);

        self::assertTrue($token->equalsAny([[\T_FUNCTION, 'function']]));
        self::assertFalse($token->equalsAny([[\T_FUNCTION, 'Function']]));
    }

    /**
     * @param list<_PhpTokenPrototypePartial|Token> $other
     *
     * @dataProvider provideEqualsAnyCases
     */
    public function testEqualsAny(bool $equalsAny, array $other, bool $caseSensitive = true): void
    {
        $token = new Token([\T_FUNCTION, 'function', 1]);

        self::assertSame($equalsAny, $token->equalsAny($other, $caseSensitive));
    }

    /**
     * @return iterable<int, array{0: bool, 1: list<_PhpTokenPrototypePartial|Token>, 2?: bool}>
     */
    public static function provideEqualsAnyCases(): iterable
    {
        $brace = self::getBraceToken();
        $foreach = self::getForeachToken();

        yield [false, []];

        yield [false, [$brace]];

        yield [false, [$brace, $foreach]];

        yield [true, [$brace, $foreach, [\T_FUNCTION]]];

        yield [true, [$brace, $foreach, [\T_FUNCTION, 'function']]];

        yield [false, [$brace, $foreach, [\T_FUNCTION, 'Function']]];

        yield [true, [$brace, $foreach, [\T_FUNCTION, 'Function']], false];

        yield [false, [[\T_VARIABLE, 'junction'], [\T_FUNCTION, 'junction']], false];
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

    /**
     * @return iterable<int, array{bool, array<int, bool>|bool, int}>
     */
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

    /**
     * @return iterable<int, array{null|string, int}>
     */
    public static function provideTokenGetNameForIdCases(): iterable
    {
        yield [
            null,
            -1,
        ];

        yield [
            'T_CLASS',
            \T_CLASS,
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

    /**
     * @return iterable<int, array{Token, null|string}>
     */
    public static function provideGetNameCases(): iterable
    {
        yield [
            new Token([\T_FUNCTION, 'function', 1]),
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

    /**
     * @return iterable<int, array{Token, array<string, mixed>}>
     */
    public static function provideToArrayCases(): iterable
    {
        yield [
            new Token([\T_FUNCTION, 'function', 1]),
            [
                'id' => \T_FUNCTION,
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

    private static function getBraceToken(): Token
    {
        return new Token('(');
    }

    private static function getForeachToken(): Token
    {
        return new Token([\T_FOREACH, 'foreach']);
    }
}
