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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer
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

    public function provideFixCases(): array
    {
        $attributesAndMethodsOriginal = $this->getAttributesAndMethods(true);
        $attributesAndMethodsFixed = $this->getAttributesAndMethods(false);

        return [
            'final-extends' => [
                "<?php final class MyClass extends MyAbstractClass { {$attributesAndMethodsOriginal} }",
            ],
            'normal-extends' => [
                "<?php class MyClass extends MyAbstractClass { {$attributesAndMethodsOriginal} }",
            ],
            'abstract' => [
                "<?php abstract class MyAbstractClass { {$attributesAndMethodsOriginal} }",
            ],
            'normal' => [
                "<?php class MyClass { {$attributesAndMethodsOriginal} }",
            ],
            'trait' => [
                "<?php trait MyTrait { {$attributesAndMethodsOriginal} }",
            ],
            'final-with-trait' => [
                "<?php final class MyClass { use MyTrait; {$attributesAndMethodsOriginal} }",
            ],
            'multiline-comment' => [
                '<?php final class MyClass { /* public protected private */ }',
            ],
            'inline-comment' => [
                "<?php final class MyClass { \n // public protected private \n }",
            ],
            'final' => [
                "<?php final class MyClass { {$attributesAndMethodsFixed} } class B {use C;}",
                "<?php final class MyClass { {$attributesAndMethodsOriginal} } class B {use C;}",
            ],
            'final-implements' => [
                "<?php final class MyClass implements MyInterface { {$attributesAndMethodsFixed} }",
                "<?php final class MyClass implements MyInterface { {$attributesAndMethodsOriginal} }",
            ],
            'final-with-use-before' => [
                "<?php use stdClass; final class MyClass { {$attributesAndMethodsFixed} }",
                "<?php use stdClass; final class MyClass { {$attributesAndMethodsOriginal} }",
            ],
            'final-with-use-after' => [
                "<?php final class MyClass { {$attributesAndMethodsFixed} } use stdClass;",
                "<?php final class MyClass { {$attributesAndMethodsOriginal} } use stdClass;",
            ],
            'multiple-classes' => [
                "<?php final class MyFirstClass { {$attributesAndMethodsFixed} } class MySecondClass { {$attributesAndMethodsOriginal} } final class MyThirdClass { {$attributesAndMethodsFixed} } ",
                "<?php final class MyFirstClass { {$attributesAndMethodsOriginal} } class MySecondClass { {$attributesAndMethodsOriginal} } final class MyThirdClass { {$attributesAndMethodsOriginal} } ",
            ],
            'minimal-set' => [
                '<?php final class MyClass { private $v1; }',
                '<?php final class MyClass { protected $v1; }',
            ],
            'anonymous-class-inside' => [
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
            ],
            [
                '<?php $a = new class{protected function A(){ echo 123; }};',
            ],
            [
                '<?php final class Foo { private int $foo; }',
                '<?php final class Foo { protected int $foo; }',
            ],
            [
                '<?php final class Foo { private ?string $foo; }',
                '<?php final class Foo { protected ?string $foo; }',
            ],
            [
                '<?php final class Foo { private array $foo; }',
                '<?php final class Foo { protected array $foo; }',
            ],
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

    public static function provideFix80Cases(): iterable
    {
        yield [
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
    }

    private function getAttributesAndMethods(bool $original): string
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
