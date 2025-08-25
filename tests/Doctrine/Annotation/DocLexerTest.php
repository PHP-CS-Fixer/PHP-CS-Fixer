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
 * @internal
 *
 * @covers \PhpCsFixer\Doctrine\Annotation\DocLexer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DocLexerTest extends TestCase
{
    public function testCreateFromEmptyPhpdocComment(): void
    {
        $lexer = new DocLexer();
        $lexer->setInput('/** @Foo("bar": 42) */');

        $expectedContents = [
            [DocLexer::T_NONE, '/', 0],
            [DocLexer::T_AT, '@', 4],
            [DocLexer::T_IDENTIFIER, 'Foo', 5],
            [DocLexer::T_OPEN_PARENTHESIS, '(', 8],
            [DocLexer::T_STRING, 'bar', 9],
            [DocLexer::T_COLON, ':', 14],
            [DocLexer::T_INTEGER, '42', 16],
            [DocLexer::T_CLOSE_PARENTHESIS, ')', 18],
            [DocLexer::T_NONE, '/', 21],
        ];

        foreach ($expectedContents as $expectedContent) {
            $token = $lexer->peek();
            self::assertInstanceOf(Token::class, $token);
            self::assertSame($expectedContent[0], $token->getType());
            self::assertSame($expectedContent[1], $token->getContent());
            self::assertSame($expectedContent[2], $token->getPosition());
        }

        self::assertNull($lexer->peek());
    }
}
