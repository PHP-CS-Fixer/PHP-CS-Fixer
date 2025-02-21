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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ReadonlyClassFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\ReadonlyClassFixer>
 */
final class ReadonlyClassFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @requires PHP 8.2
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
             class TestClass
             {
                 public function __construct(public Foo $foo){}
             }',
        ];

        yield [
            '<?php
             readonly class TestClass
             {
                 public function __construct(public Foo $foo){}
             }',
        ];

        yield [
            '<?php
             abstract class TestClass
             {
                 public function __construct(public readonly string $foo){}
             }',
        ];

        yield [
            '<?php
            readonly class TestClass
            {
                public function __construct(public string $foo){}
            }',
            '<?php
            readonly class TestClass
            {
                public function __construct(public readonly string $foo){}
            }',
        ];

        yield [
            '<?php
            readonly class TestClass
            {
                public function __construct(
                    public string $foo,
                    public int $bar
                ){}
            }',
            '<?php
            readonly class TestClass
            {
                public function __construct(
                    public readonly string $foo,
                    public readonly int $bar
                ){}
            }',
        ];

        yield [
            '<?php
            readonly class TestClass
            {
                private Foo $foo;

                public function __construct(
                    private FooFactory $fooFactory,
                    private Bar $bar,
                ) {
                    $this->foo = $fooFactory->create();
                }
            }',
            '<?php
            readonly class TestClass
            {
                private readonly Foo $foo;

                public function __construct(
                    private readonly FooFactory $fooFactory,
                    private readonly Bar $bar,
                ) {
                    $this->foo = $fooFactory->create();
                }
            }',
        ];

        yield [
            '<?php
            readonly class TestClass
            {
                private Foo $foo;

                public function __construct(
                    private FooFactory $fooFactory,
                    protected string $bar,
                    public array $baz,
                ) {
                    $this->foo = $fooFactory->create();
                }
            }',
            '<?php
            readonly class TestClass
            {
                private readonly Foo $foo;

                public function __construct(
                    private FooFactory $fooFactory,
                    protected readonly string $bar,
                    public readonly array $baz,
                ) {
                    $this->foo = $fooFactory->create();
                }
            }',
        ];

        yield [
            '<?php
            readonly class TestClass
            {
                private string $foo;
                private int $bar;

                public function __construct(
                    public FooFactory $fooFactory,
                    Baz $baz,
                    string $foo,
                    int $bar,
                ) {
                    $this->baz = $baz;
                    $this->foo = $foo;
                    $this->bar = $bar;
                }

                protected Baz $baz;
            }',
            '<?php
            readonly class TestClass
            {
                private readonly string $foo;
                private int $bar;

                public function __construct(
                    public FooFactory $fooFactory,
                    Baz $baz,
                    string $foo,
                    int $bar,
                ) {
                    $this->baz = $baz;
                    $this->foo = $foo;
                    $this->bar = $bar;
                }

                protected readonly Baz $baz;
            }',
        ];

        yield [
            '<?php
            readonly class TestClass
            {
                public function __construct(
                    private Foo $foo,
                    protected Bar $bar,
                    public array $items,
                ) {}
            }',
            '<?php
            readonly class TestClass
            {
                public function __construct(
                    private readonly Foo $foo,
                    protected readonly Bar $bar,
                    public readonly array $items,
                ) {}
            }',
        ];

        yield [
            '<?php
            abstract readonly class TestClass
            {
                public function __construct(
                    private Foo $foo,
                    protected Bar $bar,
                    public array $items,
                ) {}
            }',
            '<?php
            abstract readonly class TestClass
            {
                public function __construct(
                    private readonly Foo $foo,
                    protected readonly Bar $bar,
                    public readonly array $items,
                ) {}
            }',
        ];

        yield [
            '<?php
            final readonly class TestClass
            {
                public function __construct(
                     public string $foo,
                     public int $bar,
                ) {}
            }',
            '<?php
            final class TestClass
            {
                public function __construct(
                     public readonly string $foo,
                     public readonly int $bar,
                ) {}
            }',
        ];

        yield [
            '<?php
            final class TestClass
            {
                private string $baz = "";
                public function __construct(
                     public readonly string $foo,
                     public readonly int $bar,
                ) {}
            }',
        ];

        yield [
            '<?php
            class TestClass
            {
                private string $baz = "";
                public function __construct(
                     public readonly string $foo,
                     public readonly int $bar,
                ) {}
            }',
        ];

        yield [
            '<?php
            class TestClass
            {
                public function __construct(
                     public readonly string $foo,
                     public readonly int $bar,
                ) {}
            }',
        ];

        yield [
            '<?php
            abstract class TestClass
            {
                private string $baz = "";
                public function __construct(
                     public readonly string $foo,
                     public readonly int $bar,
                ) {}
            }',
        ];

        yield [
            '<?php
          final class TestClass
          {
            private readonly string $baz;

            public function __construct(
                protected readonly string $bar,
                string $baz
            ) {
                $this->baz = $this->bar;
            }
            }',
        ];

        yield [
            '<?php
            final readonly class TestClass
            {
                public function __construct(public string $foo){}
                public function bar(): void {}
            }',
            '<?php
            final class TestClass
            {
                public function __construct(public readonly string $foo){}
                public function bar(): void {}
            }',
        ];

        yield [
            '<?php
            final class TestClass
            {
                public static string $foo;
                public function __construct(public readonly string $bar){}
            }',
        ];

        yield [
            '<?php
            final readonly class TestClass extends ParentClass
            {
                public function __construct(public string $foo){}
            }',
            '<?php
            final class TestClass extends ParentClass
            {
                public function __construct(public readonly string $foo){}
            }',
        ];
    }
}
