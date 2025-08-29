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

namespace PhpCsFixer\Tests\Doctrine\Annotation;

use PhpCsFixer\Doctrine\Annotation\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Doctrine\Annotation\Token
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class TokenTest extends TestCase
{
    public function testDefaults(): void
    {
        $token = new Token();

        self::assertSame(DocLexer::T_NONE, $token->getType());
        self::assertSame('', $token->getContent());
        self::assertSame(0, $token->getPosition());
    }

    public function testConstructorSetsValues(): void
    {
        $type = 42;
        $content = 'questionable';
        $position = 16;

        $token = new Token(
            $type,
            $content,
            $position,
        );

        self::assertSame($type, $token->getType());
        self::assertSame($content, $token->getContent());
        self::assertSame($position, $token->getPosition());
    }

    public function testCanModifyType(): void
    {
        $type = 42;

        $token = new Token();

        $token->setType($type);

        self::assertSame($type, $token->getType());
    }

    /**
     * @dataProvider provideIsTypeReturnsTrueCases
     *
     * @param int|list<int> $types
     */
    public function testIsTypeReturnsTrue(int $type, $types): void
    {
        $token = new Token();

        $token->setType($type);

        self::assertTrue($token->isType($types));
    }

    /**
     * @return iterable<string, array{int, int|list<int>}>
     */
    public static function provideIsTypeReturnsTrueCases(): iterable
    {
        yield 'same-value' => [
            42,
            42,
        ];

        yield 'array-with-value' => [
            42,
            [
                42,
                9_001,
            ],
        ];
    }

    /**
     * @dataProvider provideIsTypeReturnsFalseCases
     *
     * @param int|list<int> $types
     */
    public function testIsTypeReturnsFalse(int $type, $types): void
    {
        $token = new Token();

        $token->setType($type);

        self::assertFalse($token->isType($types));
    }

    /**
     * @return iterable<string, array{int, int|list<int>}>
     */
    public static function provideIsTypeReturnsFalseCases(): iterable
    {
        yield 'different-value' => [
            42,
            9_001,
        ];

        yield 'array-without-value' => [
            42,
            [
                9_001,
            ],
        ];
    }

    public function testCanModifyContent(): void
    {
        $content = 'questionable';

        $token = new Token();

        $token->setContent($content);

        self::assertSame($content, $token->getContent());
    }

    public function testCanClearContent(): void
    {
        $content = 'questionable';

        $token = new Token();

        $token->setContent($content);
        $token->clear();

        self::assertSame('', $token->getContent());
    }
}
