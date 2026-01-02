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
 *
 * @phpstan-import-type _TransformerTestExpectedKindsUnderIndex from AbstractTransformerTestCase
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NullableTypeTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
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
            ],
        );
    }

    /**
     * @return iterable<int, array{0: string, 1?: _TransformerTestExpectedKindsUnderIndex}>
     */
    public static function provideProcessCases(): iterable
    {
        yield [
            '<?php function foo(?Barable $barA, ?Barable $barB): ?Fooable {}',
            [
                5 => CT::T_NULLABLE_TYPE,
                11 => CT::T_NULLABLE_TYPE,
                18 => CT::T_NULLABLE_TYPE,
            ],
        ];

        yield [
            '<?php interface Fooable { function foo(): ?Fooable; }',
            [
                14 => CT::T_NULLABLE_TYPE,
            ],
        ];

        yield [
            '<?php
                    $a = 1 ? "aaa" : "bbb";
                    $b = 1 ? fnc() : [];
                    $c = 1 ?: [];
                    $a instanceof static ? "aaa" : "bbb";
                ',
        ];

        yield [
            '<?php class Foo { private ?string $foo; }',
            [
                9 => CT::T_NULLABLE_TYPE,
            ],
        ];

        yield [
            '<?php class Foo { protected ?string $foo; }',
            [
                9 => CT::T_NULLABLE_TYPE,
            ],
        ];

        yield [
            '<?php class Foo { public ?string $foo; }',
            [
                9 => CT::T_NULLABLE_TYPE,
            ],
        ];

        yield [
            '<?php class Foo { var ?string $foo; }',
            [
                9 => CT::T_NULLABLE_TYPE,
            ],
        ];

        yield [
            '<?php class Foo { var ? Foo\Bar $foo; }',
            [
                9 => CT::T_NULLABLE_TYPE,
            ],
        ];

        yield [
            '<?php fn(?Barable $barA, ?Barable $barB): ?Fooable => null;',
            [
                3 => CT::T_NULLABLE_TYPE,
                9 => CT::T_NULLABLE_TYPE,
                16 => CT::T_NULLABLE_TYPE,
            ],
        ];

        yield [
            '<?php class Foo { public ?array $foo; public static ?array $bar; }',
            [
                9 => CT::T_NULLABLE_TYPE,
                19 => CT::T_NULLABLE_TYPE,
            ],
        ];
    }

    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcess80Cases
     *
     * @requires PHP 8.0
     */
    public function testProcess80(array $expectedTokens, string $source): void
    {
        $this->testProcess($source, $expectedTokens);
    }

    /**
     * @return iterable<int, array{_TransformerTestExpectedKindsUnderIndex, string}>
     */
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
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcess81Cases
     *
     * @requires PHP 8.1
     */
    public function testProcess81(array $expectedTokens, string $source): void
    {
        $this->testProcess($source, $expectedTokens);
    }

    /**
     * @return iterable<int, array{_TransformerTestExpectedKindsUnderIndex, string}>
     */
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

    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcess83Cases
     *
     * @requires PHP 8.3
     */
    public function testProcess83(array $expectedTokens, string $source): void
    {
        $this->testProcess($source, $expectedTokens);
    }

    /**
     * @return iterable<string, array{_TransformerTestExpectedKindsUnderIndex, string}>
     */
    public static function provideProcess83Cases(): iterable
    {
        yield 'nullable class constant' => [
            [
                12 => CT::T_NULLABLE_TYPE,
            ],
            '<?php
                class Foo
                {
                    public const ?string FOO = null;
                }
            ',
        ];
    }

    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcess84Cases
     *
     * @requires PHP 8.4
     */
    public function testProcess84(array $expectedTokens, string $source): void
    {
        $this->testProcess($source, $expectedTokens);
    }

    /**
     * @return iterable<string, array{_TransformerTestExpectedKindsUnderIndex, string}>
     */
    public static function provideProcess84Cases(): iterable
    {
        yield 'asymmetric visibility' => [
            [
                18 => CT::T_NULLABLE_TYPE,
                28 => CT::T_NULLABLE_TYPE,
                38 => CT::T_NULLABLE_TYPE,
            ],
            <<<'PHP'
                <?php
                class Foo {
                    public function __construct(
                        public public(set) ?Bar $x,
                        public protected(set) ?Bar $y,
                        public private(set) ?Bar $z,
                    ) {}
                }
                PHP,
        ];

        yield 'abstract properties' => [
            [
                13 => CT::T_NULLABLE_TYPE,
                29 => CT::T_NULLABLE_TYPE,
                45 => CT::T_NULLABLE_TYPE,
                61 => CT::T_NULLABLE_TYPE,
                77 => CT::T_NULLABLE_TYPE,
                93 => CT::T_NULLABLE_TYPE,
            ],
            <<<'PHP'
                <?php
                abstract class Foo {
                    abstract public ?bool $b1 { set; }
                    public abstract ?bool $b2 { set; }
                    abstract protected ?int $i1 { set; }
                    protected abstract ?int $i2 { set; }
                    abstract private ?string $s1 { set; }
                    private abstract ?string $s2 { set; }
                }
                PHP,
        ];

        yield 'final properties' => [
            [
                11 => CT::T_NULLABLE_TYPE,
                31 => CT::T_NULLABLE_TYPE,
                51 => CT::T_NULLABLE_TYPE,
                71 => CT::T_NULLABLE_TYPE,
                91 => CT::T_NULLABLE_TYPE,
                111 => CT::T_NULLABLE_TYPE,
            ],
            <<<'PHP'
                <?php
                class Foo {
                    final public ?bool $b1 { get => 0; }
                    public final ?bool $b2 { get => 0; }
                    final protected ?int $i1 { get => 0; }
                    protected final ?int $i2 { get => 0; }
                    final private ?string $s1 { get => 0; }
                    private final ?string $s2 { get => 0; }
                }
                PHP,
        ];
    }
}
