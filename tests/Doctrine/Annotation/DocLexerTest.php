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
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Doctrine\Annotation\DocLexer
 */
final class DocLexerTest extends TestCase
{
    public function testCreateFromEmptyPhpdocComment(): void
    {
        $lexer = new DocLexer();
        $lexer->setInput('/** @Foo("bar": 42) */');

        self::assertSame('/', $lexer->peek()->getContent());
        self::assertSame('@', $lexer->peek()->getContent());
        self::assertSame('Foo', $lexer->peek()->getContent());
        self::assertSame('(', $lexer->peek()->getContent());
        self::assertSame('bar', $lexer->peek()->getContent());
        self::assertSame(':', $lexer->peek()->getContent());
        self::assertSame('42', $lexer->peek()->getContent());
        self::assertSame(')', $lexer->peek()->getContent());
        self::assertSame('/', $lexer->peek()->getContent());
        self::assertNull($lexer->peek());
    }
}
