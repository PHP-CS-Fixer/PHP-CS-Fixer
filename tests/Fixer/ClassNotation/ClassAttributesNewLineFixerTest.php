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
 * @covers \PhpCsFixer\Fixer\ClassNotation\ClassAttributesNewLineFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\ClassAttributesNewLineFixer>
 */
final class ClassAttributesNewLineFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @requires PHP 8.0
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'basic test' => [
            '<?php
#[Foo]
class Foo
{
}',
            '<?php
#[Foo] class Foo
{
}',
        ];

        yield 'multiple attributes on class' => [
            '<?php
#[Foo]
#[Bar]
class Foo
{
}',
            '<?php
#[Foo] #[Bar] class Foo
{
}', ];

        yield '3 attributes on class' => [
            '<?php
#[Foo]
#[Bar]
#[Baz]
class Foo
{
}',
            '<?php
#[Foo] #[Bar] #[Baz]
class Foo
{
}', ];

        yield 'attribute with parameters on class' => [
            '<?php
#[Foo(name: "name", description: "description")]
class Foo extends Bar
{
}',
            '<?php
#[Foo(name: "name", description: "description")] class Foo extends Bar
{
}',
        ];

        yield 'attribute already on new line' => [
            '<?php
#[Foo]
class Foo
{
}',
        ];

        yield 'attribute on class but not method' => [
            '<?php
#[Foo]
class Foo
{
    #[Bar] private $id;

    #[Baz("baz")] public function get() {}
}',
            '<?php
#[Foo] class Foo
{
    #[Bar] private $id;

    #[Baz("baz")] public function get() {}
}',
        ];

        yield 'multiple nested attributes on class' => [
            '<?php
namespace App;

#[Foo]
#[Bar(
    param: "bar"
)]
class Example
{
    #[Baz] private $prop;
}',
            '<?php
namespace App;

#[Foo] #[Bar(
    param: "bar"
)] class Example
{
    #[Baz] private $prop;
}',
        ];

        yield 'class with comments and attributes' => [
            '<?php
// Some comment
#[Foo]
class Foo
{
}',
            '<?php
// Some comment
#[Foo] class Foo
{
}',
        ];

        yield 'multiple classes with attributes' => [
            '<?php
#[Bar]
class Foo
{
}

#[Baz]
class Bar
{
}',
            '<?php
#[Bar] class Foo
{
}

#[Baz] class Bar
{
}',
        ];

        yield 'class with docblock and attribute' => [
            '<?php
/**
 * Class documentation
 */
#[Foo]
class Foo
{
}',
            '<?php
/**
 * Class documentation
 */
#[Foo] class Foo
{
}',
        ];

        yield 'class with comment after attribute' => [
            '<?php
#[Foo]
/** test */ class Foo
{
}',
            '<?php
#[Foo] /** test */ class Foo
{
}',
        ];

        yield 'two classes with attributes nothing changes' => [
            '<?php
#[Foo]
class Bar
{
}

#[Baz]
class Baz
{
}', ];

        yield 'two classes with multiple attributes' => [
            '<?php
#[Foo, Bar]
#[Bee]
class FirstClass
{
    public function bar() {}
}

#[Baz, Bar]
#[Bee]
class SecondClass
{
    private $property;
}',
            '<?php
#[Foo, Bar] #[Bee]
class FirstClass
{
    public function bar() {}
}

#[Baz, Bar] #[Bee]
class SecondClass
{
    private $property;
}',
        ];
    }
}
