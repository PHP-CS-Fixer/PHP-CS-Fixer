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

use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Doctrine\Annotation\Tokens
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class TokensTest extends TestCase
{
    public function testCreateFromEmptyPhpdocComment(): void
    {
        $docComment = '/** */';

        $token = new Token([\T_DOC_COMMENT, $docComment]);
        $tokens = Tokens::createFromDocComment($token);

        self::assertCount(1, $tokens);
        self::assertSame($docComment, $tokens->getCode());
    }

    /**
     * @dataProvider provideOffSetOtherThanTokenCases
     */
    public function testOffSetOtherThanToken(string $message, ?string $wrongType): void
    {
        $docComment = '/** */';

        $token = new Token([\T_DOC_COMMENT, $docComment]);
        $tokens = Tokens::createFromDocComment($token);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        // @phpstan-ignore-next-line as we are testing the type error
        $tokens[1] = $wrongType;
    }

    /**
     * @return iterable<int, array{string, null|string}>
     */
    public static function provideOffSetOtherThanTokenCases(): iterable
    {
        yield [
            'Token must be an instance of PhpCsFixer\Doctrine\Annotation\Token, "null" given.',
            null,
        ];

        yield [
            'Token must be an instance of PhpCsFixer\Doctrine\Annotation\Token, "string" given.',
            'foo',
        ];
    }
}
