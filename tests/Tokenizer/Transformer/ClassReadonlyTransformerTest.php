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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @author Mateusz Sip <mateusz.sip@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\ClassReadonlyTransformer
 */
final class ClassReadonlyTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_CLASS_READONLY,
            ]
        );
    }

    public static function provideProcessCases(): array
    {
        return [
            [
                '<?php final readonly class Foo {}',
                [
                    3 => CT::T_CLASS_READONLY,
                ],
            ],
            [
                '<?php readonly class Foo {}',
                [
                    1 => CT::T_CLASS_READONLY,
                ],
            ],
            [
                <<<'PHP'
                    class Foo {
                        public readonly string $foo;
                    }
                PHP
            ],
            [
                <<<'PHP'
                    class Foo {
                        public function __construct(
                            public readonly string $foo = "foobar"
                        ) {}
                    }
                PHP
            ],
        ];
    }
}
