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

    public function provideConstructorValidationCases(): array
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

    public function testGetPrototype(): void
    {
        static::assertSame($this->getBraceTokenPrototype(), $this->getBraceToken()->getPrototype());
        static::assertSame($this->getForeachTokenPrototype(), $this->getForeachToken()->getPrototype());
    }

    public function testIsArray(): void
    {
        static::assertFalse($this->getBraceToken()->isArray());
        static::assertTrue($this->getForeachToken()->isArray());
    }

    /**
     * @dataProvider provideIsCastCases
     */
    public function testIsCast(Token $token, bool $isCast): void
    {
        static::assertSame($isCast, $token->isCast());
    }

    public function provideIsCastCases(): array
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
     * @dataProvider provideIsClassyCases
     */
    public function testIsClassy(Token $token, bool $isClassy): void
    {
        static::assertSame($isClassy, $token->isClassy());
    }

    public function provideIsClassyCases(): array
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
     * @requires PHP 8.1
     */
    public function testEnumIsClassy(): void
    {
        $enumToken = new Token([T_ENUM, 'enum', 1]);

        static::assertTrue($enumToken->isClassy());
    }

    /**
     * @dataProvider provideIsCommentCases
     */
    public function testIsComment(Token $token, bool $isComment): void
    {
        static::assertSame($isComment, $token->isComment());
    }

    public function provideIsCommentCases(): iterable
    {
        yield from [
            [$this->getBraceToken(), false],
            [$this->getForeachToken(), false],
            [new Token([T_COMMENT, '/* comment */', 1]), true],
            [new Token([T_DOC_COMMENT, '/** docs */', 1]), true],
        ];

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
        static::assertSame($isObjectOperator, $token->isObjectOperator());
    }

    public function provideIsObjectOperatorCases(): iterable
    {
        yield from [
            [$this->getBraceToken(), false],
            [$this->getForeachToken(), false],
            [new Token([T_COMMENT, '/* comment */']), false],
            [new Token([T_DOUBLE_COLON, '::']), false],
            [new Token([T_OBJECT_OPERATOR, '->']), true],
        ];

        if (\defined('T_NULLSAFE_OBJECT_OPERATOR')) {
            yield [new Token([T_NULLSAFE_OBJECT_OPERATOR, '?->']), true];
        }
    }

    public function testIsGivenKind(): void
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

    public function testIsKeywords(): void
    {
        static::assertTrue($this->getForeachToken()->isKeyword());
        static::assertFalse($this->getBraceToken()->isKeyword());
    }

    /**
     * @param ?int $tokenId
     *
     * @dataProvider provideMagicConstantCases
     */
    public function testIsMagicConstant(?int $tokenId, string $content, bool $isConstant = true): void
    {
        $token = new Token(
            null === $tokenId ? $content : [$tokenId, $content]
        );

        static::assertSame($isConstant, $token->isMagicConstant());
    }

    public function provideMagicConstantCases(): iterable
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

        foreach ([$this->getForeachToken(), $this->getBraceToken()] as $token) {
            yield [$token->getId(), $token->getContent(), false];

            yield [$token->getId(), strtolower($token->getContent()), false];
        }
    }

    /**
     * @dataProvider provideIsNativeConstantCases
     */
    public function testIsNativeConstant(Token $token, bool $isNativeConstant): void
    {
        static::assertSame($isNativeConstant, $token->isNativeConstant());
    }

    public function provideIsNativeConstantCases(): array
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
     * @dataProvider provideIsWhitespaceCases
     */
    public function testIsWhitespace(Token $token, bool $isWhitespace, ?string $whitespaces = null): void
    {
        if (null !== $whitespaces) {
            static::assertSame($isWhitespace, $token->isWhitespace($whitespaces));
        } else {
            static::assertSame($isWhitespace, $token->isWhitespace(null));
            static::assertSame($isWhitespace, $token->isWhitespace());
        }
    }

    public function provideIsWhitespaceCases(): array
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
     * @param mixed $prototype
     *
     * @dataProvider provideCreatingTokenCases
     */
    public function testCreatingToken($prototype, ?int $expectedId, ?string $expectedContent, ?bool $expectedIsArray, ?string $expectedExceptionClass = null): void
    {
        if (null !== $expectedExceptionClass) {
            $this->expectException($expectedExceptionClass);
        }

        $token = new Token($prototype);
        static::assertSame($expectedId, $token->getId());
        static::assertSame($expectedContent, $token->getContent());
        static::assertSame($expectedIsArray, $token->isArray());
    }

    public function provideCreatingTokenCases(): array
    {
        return [
            [[T_FOREACH, 'foreach'], T_FOREACH, 'foreach', true],
            ['(', null, '(', false],
            [123, null, null, null, \InvalidArgumentException::class],
            [false, null, null, null, \InvalidArgumentException::class],
            [null, null, null, null, \InvalidArgumentException::class],
        ];
    }

    public function testEqualsDefaultIsCaseSensitive(): void
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        static::assertTrue($token->equals([T_FUNCTION, 'function']));
        static::assertFalse($token->equals([T_FUNCTION, 'Function']));
    }

    /**
     * @param array{0: int, 1?: string}|string|Token $other
     *
     * @dataProvider provideEqualsCases
     */
    public function testEquals(Token $token, bool $equals, $other, bool $caseSensitive = true): void
    {
        static::assertSame($equals, $token->equals($other, $caseSensitive));
    }

    public function provideEqualsCases(): iterable
    {
        $brace = $this->getBraceToken();
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
        if (\defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG')) { // @TODO: drop condition with new MAJOR release 4.0
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

        static::assertTrue($token->equalsAny([[T_FUNCTION, 'function']]));
        static::assertFalse($token->equalsAny([[T_FUNCTION, 'Function']]));
    }

    /**
     * @param list<array{0: int, 1?: string}|string|Token> $other
     *
     * @dataProvider provideEqualsAnyCases
     */
    public function testEqualsAny(bool $equalsAny, array $other, bool $caseSensitive = true): void
    {
        $token = new Token([T_FUNCTION, 'function', 1]);

        static::assertSame($equalsAny, $token->equalsAny($other, $caseSensitive));
    }

    public function provideEqualsAnyCases(): iterable
    {
        $brace = $this->getBraceToken();
        $foreach = $this->getForeachToken();

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
     */
    public function testIsKeyCaseSensitive(bool $isKeyCaseSensitive, $caseSensitive, int $key): void
    {
        static::assertSame($isKeyCaseSensitive, Token::isKeyCaseSensitive($caseSensitive, $key));
    }

    public function provideIsKeyCaseSensitiveCases(): iterable
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
     * @dataProvider provideTokenGetNameCases
     */
    public function testTokenGetNameForId(?string $expected, int $id): void
    {
        static::assertSame($expected, Token::getNameForId($id));
    }

    public function provideTokenGetNameCases(): array
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

    /**
     * @dataProvider provideGetNameCases
     */
    public function testGetName(Token $token, ?string $expected = null): void
    {
        static::assertSame($expected, $token->getName());
    }

    public function provideGetNameCases(): iterable
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
        static::assertSame($expected, $token->toArray());
    }

    public function provideToArrayCases(): iterable
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
            static::assertSame([T_CLASS, T_TRAIT, T_INTERFACE, T_ENUM], Token::getClassyTokenKinds());
        } else {
            static::assertSame([T_CLASS, T_TRAIT, T_INTERFACE], Token::getClassyTokenKinds());
        }
    }

    private function getBraceToken(): Token
    {
        return new Token($this->getBraceTokenPrototype());
    }

    private function getBraceTokenPrototype(): string
    {
        return '(';
    }

    private function getForeachToken(): Token
    {
        return new Token($this->getForeachTokenPrototype());
    }

    /**
     * @return array{int, string}
     */
    private function getForeachTokenPrototype(): array
    {
        static $prototype = [T_FOREACH, 'foreach'];

        return $prototype;
    }
}
