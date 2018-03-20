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

namespace PhpCsFixer\tests\Tokenizer\Generator;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Generator\NamespacedStringTokenGenerator;
use PhpCsFixer\Tokenizer\Token;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Generator\NamespacedStringTokenGenerator
 */
final class NamespacedStringTokenGeneratorTest extends TestCase
{
    /**
     * @param $input
     * @param array $expected
     * @dataProvider provideGeneratorCases
     */
    public function testGenerator($input, array $expected)
    {
        $generator = new NamespacedStringTokenGenerator();
        $this->assertSame(
            $expected,
            array_map(
                function (Token $token) {
                    return $token->getContent();
                },
                $generator->generate($input)
            )
        );
    }

    public function provideGeneratorCases()
    {
        return [
            ['test', ['test']],
            ['Some\\Namespace', ['Some', '\\', 'Namespace']],
            ['Some\\Bigger\\Namespace', ['Some', '\\', 'Bigger', '\\', 'Namespace']],
        ];
    }
}
