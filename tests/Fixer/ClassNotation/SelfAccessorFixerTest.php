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
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer
 */
final class SelfAccessorFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        return [
            [
                '<?php class Foo { const BAR = self::BAZ; }',
                '<?php class Foo { const BAR = Foo::BAZ; }',
            ],
            [
                '<?php class Foo { private $bar = self::BAZ; }',
                '<?php class Foo { private $bar = fOO::BAZ; }', // case insensitive
            ],
            [
                '<?php class Foo { function bar($a = self::BAR) {} }',
                '<?php class Foo { function bar($a = Foo::BAR) {} }',
            ],
            [
                '<?php class Foo { function bar() { self::baz(); } }',
                '<?php class Foo { function bar() { Foo::baz(); } }',
            ],
            [
                '<?php class Foo { function bar() { self::class; } }',
                '<?php class Foo { function bar() { Foo::class; } }',
            ],
            [
                '<?php class Foo { function bar() { $x instanceof self; } }',
                '<?php class Foo { function bar() { $x instanceof Foo; } }',
            ],
            [
                '<?php class Foo { function bar() { new self(); } }',
                '<?php class Foo { function bar() { new Foo(); } }',
            ],
            [
                '<?php interface Foo { const BAR = self::BAZ; function bar($a = self::BAR); }',
                '<?php interface Foo { const BAR = Foo::BAZ; function bar($a = Foo::BAR); }',
            ],
            [
                '<?php class Foo { const Foo = 1; }',
            ],
            [
                '<?php class Foo { function foo() { } }',
            ],
            [
                '<?php class Foo { function bar() { new \Baz\Foo(); } }',
            ],
            [
                '<?php class Foo { function bar() { new Foo\Baz(); } }',
            ],
            [
                '<?php class Foo { function bar() { Baz\Foo::class; } }',
            ],
            [
                '<?php class Foo { function bar() { function ($a = self::BAZ) { new self(); }; } }',
                '<?php class Foo { function bar() { function ($a = Foo::BAZ) { new Foo(); }; } }',
            ],
            [
                // In trait "self" will reference the class it's used in, not the actual trait, so we can't replace "Foo" with "self" here
                '<?php trait Foo { function bar() { Foo::bar(); } } class Bar { use Foo; }',
            ],
            [
                '<?php class Foo { public function bar(self $foo, self $bar) { return new self(); } }',
                '<?php class Foo { public function bar(Foo $foo, Foo $bar) { return new Foo(); } }',
            ],
            [
                '<?php interface Foo { public function bar(self $foo, self $bar); }',
                '<?php interface Foo { public function bar(Foo $foo, Foo $bar); }',
            ],
            [
                '<?php interface Foo { public function bar(self $foo); }',
                '<?php interface Foo { public function bar(\Foo $foo); }',
            ],
            [
                '<?php namespace Foo; interface Bar { public function baz(\Bar $bar); }',
            ],
            [
                '<?php namespace Foo; interface Bar { public function baz(self $bar); }',
                '<?php namespace Foo; interface Bar { public function baz(Bar $bar); }',
            ],
            [
                '<?php namespace Foo; interface Bar { public function baz(self $bar); }',
                '<?php namespace Foo; interface Bar { public function baz(\Foo\Bar $bar); }',
            ],
            [
                '<?php namespace Foo; interface Bar { public function baz(Foo\Bar $bar); }',
            ],
            [
                '<?php namespace Foo; interface Bar { public function baz(Bar\Foo $bar); }',
            ],
            [
                '<?php
                namespace Foo\Foo2;
                interface Bar {
                    public function baz00(Foo2\Bar $bar);
                    public function baz10(\Foo2\Bar $bar);
                    public function baz20(Foo\Foo2\Bar $bar);
                    public function baz21(self $bar);
                    public function baz30(Test\Foo\Foo2\Bar $bar);
                    public function baz31(\Test\Foo\Foo2\Bar $bar);
                }',
                '<?php
                namespace Foo\Foo2;
                interface Bar {
                    public function baz00(Foo2\Bar $bar);
                    public function baz10(\Foo2\Bar $bar);
                    public function baz20(Foo\Foo2\Bar $bar);
                    public function baz21(\Foo\Foo2\Bar $bar);
                    public function baz30(Test\Foo\Foo2\Bar $bar);
                    public function baz31(\Test\Foo\Foo2\Bar $bar);
                }',
            ],
            [
                '<?php class Foo { function bar() {
                    new class() { function baz() { new Foo(); } };
                } }',
            ],
            [
                '<?php class Foo { protected $foo; function bar() { return $this->foo::find(2); } }',
            ],
            [
                '<?php class Foo { public function bar(self $foo, self $bar): self { return new self(); } }',
                '<?php class Foo { public function bar(Foo $foo, Foo $bar): Foo { return new Foo(); } }',
            ],
            [
                '<?php interface Foo { public function bar(self $foo, self $bar): self; }',
                '<?php interface Foo { public function bar(Foo $foo, Foo $bar): Foo; }',
            ],
            [
                '<?php class Foo { public function bar(?self $foo, ?self $bar): ?self { return new self(); } }',
                '<?php class Foo { public function bar(?Foo $foo, ?Foo $bar): ?Foo { return new Foo(); } }',
            ],
            [
                "<?php interface Foo{ public function bar()\t/**/:?/**/self; }",
                "<?php interface Foo{ public function bar()\t/**/:?/**/Foo; }",
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
            '<?php interface Foo { public function bar(self $foo, self $bar,): self; }',
            '<?php interface Foo { public function bar(Foo $foo, Foo $bar,): Foo; }',
        ];

        yield [
            '<?php class Foo { function bar() { $x instanceof (Foo()); } }',
        ];

        yield [
            '<?php class Foo { protected $foo; function bar() { return $this?->foo::find(2); } }',
        ];

        yield [
            '<?php class Foo { public function f(self|Bar|Baz $b) {} }',
            '<?php class Foo { public function f(Foo|Bar|Baz $b) {} }',
        ];

        yield [
            '<?php class Foo { public function f(Bar|self|Baz $b) {} }',
            '<?php class Foo { public function f(Bar|Foo|Baz $b) {} }',
        ];

        yield [
            '<?php class Foo { public function f(Bar|Baz|self $b) {} }',
            '<?php class Foo { public function f(Bar|Baz|Foo $b) {} }',
        ];

        yield [
            '<?php class Foo { public function f(Bar|Foo\C|Baz $b) {} }',
        ];

        yield [
            '<?php class Foo { public function f(Bar|C\Foo|Baz $b) {} }',
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield [
            '<?php class Foo { public function f(self|(Bar&Baz)|Qux $b) {} }',
            '<?php class Foo { public function f(Foo|(Bar&Baz)|Qux $b) {} }',
        ];

        yield [
            '<?php class Foo { public function f(Bar|(Baz&Qux)|self $b) {} }',
            '<?php class Foo { public function f(Bar|(Baz&Qux)|Foo $b) {} }',
        ];
    }
}
