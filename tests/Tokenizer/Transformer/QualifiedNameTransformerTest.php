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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\QualifiedNameTransformer
 */
final class QualifiedNameTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     * @requires PHP 8.0
     */
    public function testProcess($source, array $expectedTokens)
    {
        $tokens = Tokens::fromCode($source);

        foreach ($expectedTokens as $index => $expectedToken) {
            $token = $tokens[$index];

            static::assertSame($expectedToken[1], $token->getContent());
            static::assertSame($expectedToken[0], $token->getId());
        }
    }

    public function provideProcessCases()
    {
        return [
            [
                '<?php new Bar;',
                [
                    3 => [T_STRING, 'Bar'],
                ],
            ],
            [
                '<?php new Foo\\Bar;',
                [
                    3 => [T_STRING, 'Foo'],
                    4 => [T_NS_SEPARATOR, '\\'],
                    5 => [T_STRING, 'Bar'],
                ],
            ],
            [
                '<?php new \\Foo\\Bar;',
                [
                    4 => [T_NS_SEPARATOR, '\\'],
                    3 => [T_STRING, 'Foo'],
                    4 => [T_NS_SEPARATOR, '\\'],
                    5 => [T_STRING, 'Bar'],
                ],
            ],
            [
                '<?php namespace Foo\\Bar;',
                [
                    3 => [T_STRING, 'Foo'],
                    4 => [T_NS_SEPARATOR, '\\'],
                    5 => [T_STRING, 'Bar'],
                ],
            ],
        ];
    }
}
