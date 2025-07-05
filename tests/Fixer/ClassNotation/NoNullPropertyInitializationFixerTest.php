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
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer>
 *
 * @author ntzm
 */
final class NoNullPropertyInitializationFixerTest extends AbstractFixerTestCase
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
            '<?php class Foo { public $bar; }',
            '<?php class Foo { public $bar = null; }',
        ];

        yield [
            '<?php class Foo { protected $bar; }',
            '<?php class Foo { protected $bar = null; }',
        ];

        yield [
            '<?php class Foo { private $bar; }',
            '<?php class Foo { private $bar = null; }',
        ];

        yield [
            '<?php class Foo { var $bar; }',
            '<?php class Foo { var $bar = null; }',
        ];

        yield [
            '<?php class Foo { VAR $bar; }',
            '<?php class Foo { VAR $bar = null; }',
        ];

        yield [
            '<?php class Foo { public $bar; }',
            '<?php class Foo { public $bar = NULL; }',
        ];

        yield [
            '<?php class Foo { PUblic $bar; }',
            '<?php class Foo { PUblic $bar = nuLL; }',
        ];

        yield [
            '<?php trait Foo { public $bar; }',
            '<?php trait Foo { public $bar = nuLL; }',
        ];

        yield [
            '<?php class Foo { public $bar; }',
            '<?php class Foo { public $bar = \null; }',
        ];

        yield [
            '<?php class Foo {/* */public/* A */$bar/* B *//** C */;/* D */}',
            '<?php class Foo {/* */public/* A */$bar/* B */=/** C */null;/* D */}',
        ];

        yield [
            '<?php class Foo { public $bar; protected $baz; }',
            '<?php class Foo { public $bar = null; protected $baz = null; }',
        ];

        yield [
            '<?php class Foo { public $bar = \'null\'; }',
        ];

        yield [
            '<?php class Foo { public function bar() { return null; } }',
        ];

        yield [
            '<?php class Foo { protected $bar, $baz, $qux; }',
            '<?php class Foo { protected $bar = null, $baz = null, $qux = null; }',
        ];

        yield [
            '<?php class Foo { protected $bar, $baz = \'baz\', $qux; }',
            '<?php class Foo { protected $bar, $baz = \'baz\', $qux = null; }',
        ];

        yield [
            '<?php trait Foo { public $bar; } abstract class Bar { protected $bar, $baz = \'baz\', $qux; }',
            '<?php trait Foo { public $bar = null; } abstract class Bar { protected $bar, $baz = \'baz\', $qux = null; }',
        ];

        yield [
            '<?php class Foo { public function foo() { return null; } public $bar; public function baz() { return null; } }',
            '<?php class Foo { public function foo() { return null; } public $bar = null; public function baz() { return null; } }',
        ];

        yield [
            '<?php class#1
Foo#2
{#3
protected#4
$bar#5
#6
,#7
$baz#8
#9
,#10
$qux#11
#12
;#13
}
',
            '<?php class#1
Foo#2
{#3
protected#4
$bar#5
=#6
null,#7
$baz#8
=#9
null,#10
$qux#11
=#12
null;#13
}
',
        ];

        yield [
            '<?php class Foo { public static $bar; }',
            '<?php class Foo { public static $bar = null; }',
        ];

        yield [
            '<?php class Foo { protected static $bar; }',
            '<?php class Foo { protected static $bar = null; }',
        ];

        yield [
            '<?php class Foo { private static $bar; }',
            '<?php class Foo { private static $bar = null; }',
        ];

        yield [
            '<?php class Foo { static $bar; }',
            '<?php class Foo { static $bar = null; }',
        ];

        yield [
            '<?php class Foo { STATIC $bar; }',
            '<?php class Foo { STATIC $bar = null; }',
        ];

        yield [
            '<?php class Foo { public static $bar; }',
            '<?php class Foo { public static $bar = NULL; }',
        ];

        yield [
            '<?php class Foo { PUblic STatic $bar; }',
            '<?php class Foo { PUblic STatic $bar = nuLL; }',
        ];

        yield [
            '<?php trait Foo { public static $bar; }',
            '<?php trait Foo { public static $bar = nuLL; }',
        ];

        yield [
            '<?php class Foo { public static $bar; }',
            '<?php class Foo { public static $bar = \null; }',
        ];

        yield [
            '<?php class Foo {/* */public/* */static/* A */$bar/* B *//** C */;/* D */}',
            '<?php class Foo {/* */public/* */static/* A */$bar/* B */=/** C */null;/* D */}',
        ];

        yield [
            '<?php class Foo { public static $bar; protected static $baz; }',
            '<?php class Foo { public static $bar = null; protected static $baz = null; }',
        ];

        yield [
            '<?php class Foo { public static $bar = \'null\'; }',
        ];

        yield [
            '<?php class Foo { public static function bar() { return null; } }',
        ];

        yield [
            '<?php class Foo { protected static $bar, $baz, $qux; }',
            '<?php class Foo { protected static $bar = null, $baz = null, $qux = null; }',
        ];

        yield [
            '<?php class Foo { protected static $bar, $baz = \'baz\', $qux; }',
            '<?php class Foo { protected static $bar, $baz = \'baz\', $qux = null; }',
        ];

        yield [
            '<?php trait Foo { public static $bar; } abstract class Bar { protected static $bar, $baz = \'baz\', $qux; }',
            '<?php trait Foo { public static $bar = null; } abstract class Bar { protected static $bar, $baz = \'baz\', $qux = null; }',
        ];

        yield [
            '<?php class Foo { public function foo() { return null; } public static $bar; public function baz() { return null; } }',
            '<?php class Foo { public function foo() { return null; } public static $bar = null; public function baz() { return null; } }',
        ];

        yield [
            '<?php class#1
Foo#2
{#3
protected#4
static#4.5
$bar#5
#6
,#7
$baz#8
#9
,#10
$qux#11
#12
;#13
}
',
            '<?php class#1
Foo#2
{#3
protected#4
static#4.5
$bar#5
=#6
null,#7
$baz#8
=#9
null,#10
$qux#11
=#12
null;#13
}
',
        ];

        yield [
            '<?php class Foo { const FOO = null; }',
        ];

        yield [
            '<?php class Foo { public function foo() { static $foo = null; } }',
        ];

        yield [
            '<?php function foo() { static $foo = null; }',
        ];

        yield [
            '<?php new class () { public $bar; };',
            '<?php new class () { public $bar = null; };',
        ];

        yield [
            '<?php class Foo { public function foo() { return new class() { private $bar; }; } }',
            '<?php class Foo { public function foo() { return new class() { private $bar = null; }; } }',
        ];

        yield [
            '<?php class Foo { public function foo() { return new class() { private $bar; }; } } trait Baz { public $baz; }',
            '<?php class Foo { public function foo() { return new class() { private $bar = null; }; } } trait Baz { public $baz = null; }',
        ];

        yield [
            '<?php new class () { public static $bar; };',
            '<?php new class () { public static $bar = null; };',
        ];

        yield [
            '<?php class Foo { public function foo() { return new class() { private static $bar; }; } }',
            '<?php class Foo { public function foo() { return new class() { private static $bar = null; }; } }',
        ];

        yield [
            '<?php class Foo { public function foo() { return new class() { private static $bar; }; } } trait Baz { public static $baz; }',
            '<?php class Foo { public function foo() { return new class() { private static $bar = null; }; } } trait Baz { public static $baz = null; }',
        ];

        yield [
            '<?php class Foo { public function foo() { return new class() { public function foo() { static $foo = null; } }; } }',
        ];

        yield [
            '<?php function foo() { return new class() { public function foo() { static $foo = null; } }; }',
        ];

        yield [
            '<?php class Foo { public const FOO = null; }',
        ];

        yield [
            '<?php class Foo { protected ?int $bar = null; }',
        ];

        yield [
            '<?php class Foo { protected ? string $bar = null; }',
        ];

        yield [
            '<?php class Foo { protected ? array $bar = null; }',
        ];

        yield [
            '<?php class Foo { protected static ?int $bar = null; }',
        ];

        yield [
            '<?php class Foo { protected static ? string $bar = null; }',
        ];

        yield [
            '<?php class Foo { protected static ? array $bar = null; }',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php class Foo { public $bar; }',
            '<?php class Foo { public $bar = \     null; }',
        ];

        yield [
            '<?php class Foo { public $bar/* oh hai! */; }',
            '<?php class Foo { public $bar = \/* oh hai! */null; }',
        ];

        yield [
            '<?php class Foo { public static $bar; }',
            '<?php class Foo { public static $bar = \     null; }',
        ];

        yield [
            '<?php class Foo { public static $bar/* oh hai! */; }',
            '<?php class Foo { public static $bar = \/* oh hai! */null; }',
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testFix80(): void
    {
        $this->doTest('<?php
class Point {
    public function __construct(
        public ?float $x = null,
        protected ?float $y = null,
        private ?float $z = null,
    ) {}
}
');
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
     * @return iterable<string, array{string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'readonly - cannot have default value, fixer should not crash' => [
            '<?php
final class Foo
{
    public readonly string $prop;
}',
        ];
    }
}
