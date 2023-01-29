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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\NullableTypeTransformer
 */
final class NullableTypeTransformerTest extends AbstractTransformerTestCase
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
                CT::T_NULLABLE_TYPE,
            ]
        );
    }

    public static function provideProcessCases(): array
    {
        return [
            [
                '<?php function foo(?Barable $barA, ?Barable $barB): ?Fooable {}',
                [
                    5 => CT::T_NULLABLE_TYPE,
                    11 => CT::T_NULLABLE_TYPE,
                    18 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php interface Fooable { function foo(): ?Fooable; }',
                [
                    14 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php
                    $a = 1 ? "aaa" : "bbb";
                    $b = 1 ? fnc() : [];
                    $c = 1 ?: [];
                ',
            ],
            [
                '<?php class Foo { private ?string $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { protected ?string $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { public ?string $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { var ?string $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { var ? Foo\Bar $foo; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php fn(?Barable $barA, ?Barable $barB): ?Fooable => null;',
                [
                    3 => CT::T_NULLABLE_TYPE,
                    9 => CT::T_NULLABLE_TYPE,
                    16 => CT::T_NULLABLE_TYPE,
                ],
            ],
            [
                '<?php class Foo { public ?array $foo; public static ?array $bar; }',
                [
                    9 => CT::T_NULLABLE_TYPE,
                    19 => CT::T_NULLABLE_TYPE,
                ],
            ],
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcess80Cases
     *
     * @requires PHP 8.0
     */
    public function testProcess80(array $expectedTokens, string $source): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_NULLABLE_TYPE,
            ]
        );
    }

    public static function provideProcess80Cases(): iterable
    {
        yield [
            [
                17 => CT::T_NULLABLE_TYPE,
                29 => CT::T_NULLABLE_TYPE,
                41 => CT::T_NULLABLE_TYPE,
            ],
            '<?php
                class Foo
                {
                    public function __construct(
                        private ?string $foo = null,
                        protected ?string $bar = null,
                        public ?string $xyz = null,
                    ) {
                    }
                }
            ',
        ];

        yield [
            [
                10 => CT::T_NULLABLE_TYPE,
            ],
            '<?php
                function test(#[TestAttribute] ?User $user) {}
            ',
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcess81Cases
     *
     * @requires PHP 8.1
     */
    public function testProcess81(array $expectedTokens, string $source): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_NULLABLE_TYPE,
            ]
        );
    }

    public static function provideProcess81Cases(): iterable
    {
        yield [
            [
                19 => CT::T_NULLABLE_TYPE,
                33 => CT::T_NULLABLE_TYPE,
                47 => CT::T_NULLABLE_TYPE,
            ],
            '<?php
                class Foo
                {
                    public function __construct(
                        private readonly ?string $foo = null,
                        protected readonly ?string $bar = null,
                        public readonly ?string $xyz = null,
                    ) {
                    }
                }
            ',
        ];
    }
}
