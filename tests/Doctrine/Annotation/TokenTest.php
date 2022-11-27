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

use Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Doctrine\Annotation\Token
 */
final class TokenTest extends TestCase
{
    public function testDefaults(): void
    {
        $token = new Token();

        static::assertSame(DocLexer::T_NONE, $token->getType());
        static::assertSame('', $token->getContent());
    }

    public function testConstructorSetsValues(): void
    {
        $type = 42;
        $content = 'questionable';

        $token = new Token(
            $type,
            $content
        );

        static::assertSame($type, $token->getType());
        static::assertSame($content, $token->getContent());
    }

    public function testCanModifyType(): void
    {
        $type = 42;

        $token = new Token();

        $token->setType($type);

        static::assertSame($type, $token->getType());
    }

    /**
     * @dataProvider provideIsTypeCases
     *
     * @param int|int[] $types
     */
    public function testIsTypeReturnsTrue(int $type, $types): void
    {
        $token = new Token();

        $token->setType($type);

        static::assertTrue($token->isType($types));
    }

    public static function provideIsTypeCases(): array
    {
        return [
            'same-value' => [
                42,
                42,
            ],
            'array-with-value' => [
                42,
                [
                    42,
                    9001,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideIsNotTypeCases
     *
     * @param int|int[] $types
     */
    public function testIsTypeReturnsFalse(int $type, $types): void
    {
        $token = new Token();

        $token->setType($type);

        static::assertFalse($token->isType($types));
    }

    public static function provideIsNotTypeCases(): array
    {
        return [
            'different-value' => [
                42,
                9001,
            ],
            'array-without-value' => [
                42,
                [
                    9001,
                ],
            ],
        ];
    }

    public function testCanModifyContent(): void
    {
        $content = 'questionable';

        $token = new Token();

        $token->setContent($content);

        static::assertSame($content, $token->getContent());
    }

    public function testCanClearContent(): void
    {
        $content = 'questionable';

        $token = new Token();

        $token->setContent($content);
        $token->clear();

        static::assertSame('', $token->getContent());
    }
}
