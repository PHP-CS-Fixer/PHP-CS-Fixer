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

namespace PhpCsFixer\Tests\Tokenizer\Generator;

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
     * @dataProvider provideGeneratorCases
     */
    public function testGenerator(array $expected, string $input): void
    {
        $generator = new NamespacedStringTokenGenerator();

        static::assertSame(
            $expected,
            array_map(
                static function (Token $token): string {
                    return $token->getContent();
                },
                $generator->generate($input)
            )
        );
    }

    public function provideGeneratorCases(): \Generator
    {
        yield [['test'], 'test'];

        yield [['Some', '\\', 'Namespace'], 'Some\\Namespace'];

        yield [['Some', '\\', 'Bigger', '\\', 'Namespace'], 'Some\\Bigger\\Namespace'];
    }
}
