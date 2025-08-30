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
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformers
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class TransformersTest extends TestCase
{
    /**
     * @param array<int, int> $expectedTokenKinds
     *
     * @dataProvider provideTransformCases
     */
    public function testTransform(string $input, array $expectedTokenKinds): void
    {
        $tokens = Tokens::fromCode($input);

        foreach ($expectedTokenKinds as $index => $expected) {
            self::assertTrue($tokens->offsetExists($index));
            self::assertTrue($tokens[$index]->isKind($expected));
        }
    }

    /**
     * @return iterable<string, array{string, array<int, int>}>
     */
    public static function provideTransformCases(): iterable
    {
        yield 'use trait after complex string variable' => [
            <<<'SOURCE'
                <?php

                class TransformTest extends TestCase
                {
                    public function testSomething()
                    {
                        $a = 1;
                        $this->assertSame('1', "{$a}");
                    }

                    use TestTrait;

                    public function testUsingTrait()
                    {
                        $this->testTraitFunction();
                    }
                }

                SOURCE,
            [46 => CT::T_USE_TRAIT],
        ];
    }
}
