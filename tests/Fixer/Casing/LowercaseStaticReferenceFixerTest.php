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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer>
 *
 * @covers \PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer
 *
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class LowercaseStaticReferenceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php class Foo extends Bar { public function baz() { self::qux(); } }',
            '<?php class Foo extends Bar { public function baz() { SELF::qux(); } }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() { static::qux(); } }',
            '<?php class Foo extends Bar { public function baz() { STATIC::qux(); } }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() { parent::baz(); } }',
            '<?php class Foo extends Bar { public function baz() { PARENT::baz(); } }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() { parent::baz(); } }',
            '<?php class Foo extends Bar { public function baz() { Parent::baz(); } }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() { return new self(); } }',
            '<?php class Foo extends Bar { public function baz() { return new Self(); } }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() { return SelfFoo::FOO; } }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() { return FooSelf::FOO; } }',
        ];

        yield [
            '<?php class Foo extends Bar { private STATIC $baz; }',
        ];

        yield [
            '<?php class Foo extends Bar { STATIC private $baz; }',
        ];

        yield [
            '<?php class Foo extends Bar { public function paRent() {} }',
        ];

        yield [
            '<?php $foo->Self();',
        ];

        yield [
            '<?php Foo::Self();',
        ];

        yield [
            '<?php if ($foo instanceof self) { return true; }',
            '<?php if ($foo instanceof Self) { return true; }',
        ];

        yield [
            '<?php if ($foo instanceof static) { return true; }',
            '<?php if ($foo instanceof Static) { return true; }',
        ];

        yield [
            '<?php if ($foo instanceof parent) { return true; }',
            '<?php if ($foo instanceof Parent) { return true; }',
        ];

        yield [
            '<?php if ($foo instanceof Self\Bar) { return true; }',
        ];

        yield [
            '<?php if ($foo instanceof MySelf) { return true; }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz(self $x) {} }',
            '<?php class Foo extends Bar { public function baz(Self $x) {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz(parent $x) {} }',
            '<?php class Foo extends Bar { public function baz(Parent $x) {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz(MySelf $x) {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz(Self\Qux $x) {} }',
        ];

        yield [
            '<?php $a = STATIC function() {};',
        ];

        yield [
            '<?php class A { public function B() { STATIC $a; echo $a; }}',
        ];

        yield [
            '<?php class A { public function B() { $collection = $static ? new static($b) : new self(); } }',
            '<?php class A { public function B() { $collection = $static ? new STATIC($b) : new self(); } }',
        ];

        yield [
            '<?php class A { STATIC public function B() {} }',
        ];

        yield [
            '<?php
                    $a = function () {
                        STATIC $B = false;
                        if ($B) {
                            echo 1;
                        }
                        $B = true;
                    };
                ',
        ];

        yield [
            '<?php class A { const PARENT = 42; }',
        ];

        yield [
            '<?php namespace Foo\Parent;',
        ];

        yield [
            '<?php namespace Parent\Foo;',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() : self {} }',
            '<?php class Foo extends Bar { public function baz() : Self {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() : parent {} }',
            '<?php class Foo extends Bar { public function baz() : Parent {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() : MySelf {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() : Self\Qux {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz(?self $x) {} }',
            '<?php class Foo extends Bar { public function baz(?Self $x) {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz(?parent $x) {} }',
            '<?php class Foo extends Bar { public function baz(?Parent $x) {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() : ?self {} }',
            '<?php class Foo extends Bar { public function baz() : ?Self {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() : ?parent {} }',
            '<?php class Foo extends Bar { public function baz() : ?Parent {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() : ?MySelf {} }',
        ];

        yield [
            '<?php class Foo extends Bar { public function baz() : ?Self\Qux {} }',
        ];

        yield [
            '<?php class Foo {
                private STATIC int $baz1;
                private STATIC ?int $baz2;
            }',
        ];

        yield [
            '<?php
                class Foo { public function bar() {} }
                class FooChild extends Foo
                {
                    public function bar()
                    {
                        switch (true) {
                            case parent::bar():
                        }
                    }
                }',
            '<?php
                class Foo { public function bar() {} }
                class FooChild extends Foo
                {
                    public function bar()
                    {
                        switch (true) {
                            case PARENT::bar():
                        }
                    }
                }',
        ];

        yield [
            <<<'PHP'
                <?php
                class Foo {
                    public    self $a;
                    protected self $b;
                    private   self $c;
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo {
                    public    SELF $a;
                    protected SELF $b;
                    private   SELF $c;
                }
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php
                define("SELF", "foo");
                define("PARENT", "bar");
                bar(SELF);
                echo PARENT;
                class Foo {
                    public static function f()
                    {
                        return SELF;
                    }
                }
                PHP,
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
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield ['<?php $foo?->Self();'];

        yield [
            '<?php class Foo extends A {
                public function baz1() : int|parent {}
                public function baz2() : parent|int {}
                public function baz3() : ?parent {}
            }',
            '<?php class Foo extends A {
                public function baz1() : int|Parent {}
                public function baz2() : Parent|int {}
                public function baz3() : ?Parent {}
            }',
        ];

        yield [
            '<?php class Foo extends A {
                public function baz1() : int|static {}
                public function baz2() : static|int {}
                public function baz3() : ?static {}
            }',
            '<?php class Foo extends A {
                public function baz1() : int|STATIC {}
                public function baz2() : STATIC|int {}
                public function baz3() : ?STATIC {}
            }',
        ];

        yield [
            '<?php
class Foo
{
    private int|self $prop1, $prop2;
    private self|int $prop3, $prop4;
}
',
            '<?php
class Foo
{
    private int|SELF $prop1, $prop2;
    private SELF|int $prop3, $prop4;
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

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php class A { final const PARENT = 42; }',
        ];

        yield [<<<'PHP'
            <?php enum Foo: string
            {
                case SELF = 'self';
                case STATIC = 'static';
                case PARENT = 'parent';
            }
            PHP];

        yield [<<<'PHP'
            <?php enum Foo
            {
                case SELF;
                case STATIC;
                case PARENT;
            }
            PHP];
    }

    /**
     * @dataProvider provideFix83Cases
     *
     * @requires PHP 8.3
     */
    public function testFix83(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: null|string}>
     */
    public static function provideFix83Cases(): iterable
    {
        yield [<<<'PHP'
            <?php
            class Foo {
                private const array PARENT = ['parent'];
                private const array SELF = ['self'];
                private const array STATIC = ['static'];
            }
            PHP];

        yield [<<<'PHP'
            <?php
            class Foo {
                private const int PARENT = 1;
                private const int SELF = 2;
                private const int STATIC = 3;
            }
            PHP];

        yield [<<<'PHP'
            <?php
            class Foo {
                private const int|static PARENT = 1;
                private const int|static SELF = 2;
                private const int|static STATIC = 3;
            }
            PHP];

        yield [<<<'PHP'
            <?php
            class Foo {
                private const string|(Bar&Baz) PARENT = 'parent';
                private const string|(Bar&Baz) SELF = 'self';
                private const string|(Bar&Baz) STATIC = 'static';
            }
            PHP];
    }
}
