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
 * @covers \PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer>
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class ProtectedToPrivateFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        $attributesAndMethodsOriginal = self::getAttributesAndMethods(true);
        $attributesAndMethodsFixed = self::getAttributesAndMethods(false);

        yield 'final-extends' => [
            "<?php final class MyClass extends MyAbstractClass { {$attributesAndMethodsOriginal} }",
        ];

        yield 'normal-extends' => [
            "<?php class MyClass extends MyAbstractClass { {$attributesAndMethodsOriginal} }",
        ];

        yield 'abstract' => [
            "<?php abstract class MyAbstractClass { {$attributesAndMethodsOriginal} }",
        ];

        yield 'normal' => [
            "<?php class MyClass { {$attributesAndMethodsOriginal} }",
        ];

        yield 'trait' => [
            "<?php trait MyTrait { {$attributesAndMethodsOriginal} }",
        ];

        yield 'final-with-trait' => [
            "<?php final class MyClass { use MyTrait; {$attributesAndMethodsOriginal} }",
        ];

        yield 'multiline-comment' => [
            '<?php final class MyClass { /* public protected private */ }',
        ];

        yield 'inline-comment' => [
            "<?php final class MyClass { \n // public protected private \n }",
        ];

        yield 'final' => [
            "<?php final class MyClass { {$attributesAndMethodsFixed} } class B {use C;}",
            "<?php final class MyClass { {$attributesAndMethodsOriginal} } class B {use C;}",
        ];

        yield 'final-implements' => [
            "<?php final class MyClass implements MyInterface { {$attributesAndMethodsFixed} }",
            "<?php final class MyClass implements MyInterface { {$attributesAndMethodsOriginal} }",
        ];

        yield 'final-with-use-before' => [
            "<?php use stdClass; final class MyClass { {$attributesAndMethodsFixed} }",
            "<?php use stdClass; final class MyClass { {$attributesAndMethodsOriginal} }",
        ];

        yield 'final-with-use-after' => [
            "<?php final class MyClass { {$attributesAndMethodsFixed} } use stdClass;",
            "<?php final class MyClass { {$attributesAndMethodsOriginal} } use stdClass;",
        ];

        yield 'multiple-classes' => [
            "<?php final class MyFirstClass { {$attributesAndMethodsFixed} } class MySecondClass { {$attributesAndMethodsOriginal} } final class MyThirdClass { {$attributesAndMethodsFixed} } ",
            "<?php final class MyFirstClass { {$attributesAndMethodsOriginal} } class MySecondClass { {$attributesAndMethodsOriginal} } final class MyThirdClass { {$attributesAndMethodsOriginal} } ",
        ];

        yield 'minimal-set' => [
            '<?php final class MyClass { private $v1; }',
            '<?php final class MyClass { protected $v1; }',
        ];

        yield 'anonymous-class-inside' => [
            "<?php
final class Foo
{
    {$attributesAndMethodsFixed}

    private function bar()
    {
        new class {
            {$attributesAndMethodsOriginal}
        };
    }
}
",
            "<?php
final class Foo
{
    {$attributesAndMethodsOriginal}

    protected function bar()
    {
        new class {
            {$attributesAndMethodsOriginal}
        };
    }
}
",
        ];

        yield [
            '<?php $a = new class{protected function A(){ echo 123; }};',
        ];

        yield [
            '<?php final class Foo { private int $foo; }',
            '<?php final class Foo { protected int $foo; }',
        ];

        yield [
            '<?php final class Foo { private ?string $foo; }',
            '<?php final class Foo { protected ?string $foo; }',
        ];

        yield [
            '<?php final class Foo { private array $foo; }',
            '<?php final class Foo { protected array $foo; }',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'type union' => [
            '<?php
final class Foo2 {
    private int|float $a;
}
',
            '<?php
final class Foo2 {
    protected int|float $a;
}
',
        ];

        yield 'promoted properties' => [
            <<<'PHP'
                <?php final class Foo {
                    public function __construct(
                        private null|Bar $x,
                        private ?Bar $u,
                    ) {}
                }
                PHP,
            <<<'PHP'
                <?php final class Foo {
                    public function __construct(
                        protected null|Bar $x,
                        protected ?Bar $u,
                    ) {}
                }
                PHP,
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php
                final class Foo { private readonly int $d; }
            ',
            '<?php
                final class Foo { protected readonly int $d; }
            ',
        ];

        yield 'protected final const' => [
            // '<?php final class Foo { final private const Y = "i"; }', 'Fatal error: Private constant Foo::Y cannot be final as it is not visible to other classes on line 1.
            '<?php
                final class Foo1 { final protected const Y = "abc"; }
                final class Foo2 { protected final const Y = "def"; }
            ',
        ];

        yield [
            '<?php final class Foo2 { private const X = "tty"; }',
            '<?php final class Foo2 { protected const X = "tty"; }',
        ];

        yield [
            '<?php final class Foo { private Foo1&Bar $foo; }',
            '<?php final class Foo { protected Foo1&Bar $foo; }',
        ];

        // https://wiki.php.net/rfc/enumerations
        // Methods may be public, private, or protected, although in practice private and protected are equivalent as inheritance is not allowed.

        yield 'enum' => [
            '<?php
enum Foo: string
{
    private const Spades = 123;

    case Hearts = "H";

    private function test() {
        echo 123;
    }
}

Foo::Hearts->test();
            ',
            '<?php
enum Foo: string
{
    protected const Spades = 123;

    case Hearts = "H";

    protected function test() {
        echo 123;
    }
}

Foo::Hearts->test();
            ',
        ];

        yield 'enum with trait' => [
            '<?php

trait NamedDocumentStatus
{
    public function getStatusName(): string
    {
        return $this->getFoo();
    }
}

enum DocumentStats {
    use NamedDocumentStatus;

    case DRAFT;

    private function getFoo(): string {
        return "X";
    }
}

echo DocumentStats::DRAFT->getStatusName();
',
            '<?php

trait NamedDocumentStatus
{
    public function getStatusName(): string
    {
        return $this->getFoo();
    }
}

enum DocumentStats {
    use NamedDocumentStatus;

    case DRAFT;

    protected function getFoo(): string {
        return "X";
    }
}

echo DocumentStats::DRAFT->getStatusName();
',
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix82Cases(): iterable
    {
        yield 'final readonly' => [
            '<?php
            final readonly class Foo {
                private function noop(): void{}
            }',
            '<?php
            final readonly class Foo {
                protected function noop(): void{}
            }',
        ];

        yield 'final readonly reversed' => [
            '<?php
            readonly final class Foo {
                private function noop(): void{}
            }',
            '<?php
            readonly final class Foo {
                protected function noop(): void{}
            }',
        ];
    }

    /**
     * @dataProvider provideFix84Cases
     *
     * @requires PHP >= 8.4
     */
    public function testFix84(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield 'asymmetric visibility with only set visibility' => [
            '<?php
            final class Foo {
                private(set) int $a;
            }',
            '<?php
            final class Foo {
                protected(set) int $a;
            }',
        ];

        yield 'asymmetric visibility with both visibilities' => [
            '<?php
            final class Foo {
                public private(set) int $a;
                private private(set) int $b;
                private private(set) int $c;
            }',
            '<?php
            final class Foo {
                public protected(set) int $a;
                protected protected(set) int $b;
                protected private(set) int $c;
            }',
        ];
    }

    private static function getAttributesAndMethods(bool $original): string
    {
        $attributesAndMethodsOriginal = '
public $v1;
protected $v2;
private $v3;
public static $v4;
protected static $v5;
private static $v6;
public function f1(){}
protected function f2(){}
private function f3(){}
public static function f4(){}
protected static function f5(){}
private static function f6(){}
';
        if ($original) {
            return $attributesAndMethodsOriginal;
        }

        return str_replace('protected', 'private', $attributesAndMethodsOriginal);
    }
}
