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
    public function testDefaults()
    {
        $token = new Token();

        $this->assertSame(DocLexer::T_NONE, $token->getType());
        $this->assertSame('', $token->getContent());
    }

    public function testConstructorSetsValues()
    {
        $type = 42;
        $content = 'questionable';

        $token = new Token(
            $type,
            $content
        );

        $this->assertSame($type, $token->getType());
        $this->assertSame($content, $token->getContent());
    }

    public function testCanModifyType()
    {
        $type = 42;

        $token = new Token();

        $token->setType($type);

        $this->assertSame($type, $token->getType());
    }

    /**
     * @dataProvider provideIsTypeCases
     *
     * @param int       $type
     * @param int|int[] $types
     */
    public function testIsTypeReturnsTrue($type, $types)
    {
        $token = new Token();

        $token->setType($type);

        $this->assertTrue($token->isType($types));
    }

    /**
     * @return array
     */
    public function provideIsTypeCases()
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
     * @param int       $type
     * @param int|int[] $types
     */
    public function testIsTypeReturnsFalse($types, $type)
    {
        $token = new Token();

        $token->setType($type);

        $this->assertFalse($token->isType($types));
    }

    /**
     * @return array
     */
    public function provideIsNotTypeCases()
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

    public function testCanModifyContent()
    {
        $content = 'questionable';

        $token = new Token();

        $token->setContent($content);

        $this->assertSame($content, $token->getContent());
    }

    public function testCanClearContent()
    {
        $content = 'questionable';

        $token = new Token();

        $token->setContent($content);
        $token->clear();

        $this->assertSame('', $token->getContent());
    }
}
