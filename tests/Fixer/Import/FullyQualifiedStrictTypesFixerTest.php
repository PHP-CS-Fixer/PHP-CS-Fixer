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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer
 */
final class FullyQualifiedStrictTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, bool> $config
     *
     * @dataProvider provideNewLogicCases
     */
    public function testNewLogic(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideNewLogicCases(): iterable
    {
        yield 'namespace === type name' => [
            '<?php
namespace Foo\Bar;
function test(\Foo\Bar $x) {}',
        ];

        yield 'reserved type' => [
            '<?php

function test(int $x): void {}',
        ];

        yield 'namespace cases' => [
            '<?php

namespace A\B\C\D
{
    class Foo {}
}

namespace A\B\C\D\E
{
    class Bar {}
}

namespace A\B\C\D
{
    function A(Foo $fix, \X\B\C\D\E\Bar $doNotFix) {}
}
',
            '<?php

namespace A\B\C\D
{
    class Foo {}
}

namespace A\B\C\D\E
{
    class Bar {}
}

namespace A\B\C\D
{
    function A(\A\B\C\D\Foo $fix, \X\B\C\D\E\Bar $doNotFix) {}
}
',
        ];

        yield 'simple use' => [
            '<?php use A\Exception; function foo(Exception $e) {}',
            '<?php use A\Exception; function foo(\A\Exception $e) {}',
        ];

        yield 'simple use with global' => [
            '<?php use A\Exception; function foo(Exception $e, \Exception $e2) {}',
            '<?php use A\Exception; function foo(\A\Exception $e, \Exception $e2) {}',
        ];

        yield 'no backslash with global' => [
            '<?php use A\Exception; function foo(Exception $e, Foo $e2) {}',
            '<?php use A\Exception; function foo(A\Exception $e, \Foo $e2) {}',
        ];

        yield 'leading backslash in global namespace' => [
            '<?php use A\Exception; function foo(Exception $e, \Foo $e2) {}',
            '<?php use A\Exception; function foo(A\Exception $e, Foo $e2) {}',
            ['leading_backslash_in_global_namespace' => true],
        ];

        yield 'backslash must be kept when conflicts with other use with global' => [
            '<?php use A\Exception; function foo(Exception $e, \Exception $e2) {}',
        ];

        yield 'simple use as' => [
            '<?php use A\Exception as C; function foo(C $e) {}',
            '<?php use A\Exception as C; function foo(\A\Exception $e) {}',
        ];

        yield 'simple use as casing' => [
            '<?php use A\Exception as C; function foo(C $e) {}',
            '<?php use A\Exception as C; function foo(\A\EXCEPTION $e) {}',
        ];

        yield 'simple use 2' => [
            '<?php use \A\Exception; function foo(Exception $e) {}',
            '<?php use \A\Exception; function foo(\A\Exception $e) {}',
        ];

        yield 'common prefix 1' => [
            '<?php namespace Foo; function foo(\FooBar $v): \FooBar {}',
        ];

        yield 'common prefix 2' => [
            '<?php namespace Foo; function foo(\FooBar\Baz $v): \FooBar {}',
        ];

        yield 'issue #7025 - non-empty namespace, import and FQCN in argument' => [
            '<?php namespace foo\bar\baz;

use foo\baz\buzz;

class A {
    public function b(buzz $buzz): void {
    }
}',
            '<?php namespace foo\bar\baz;

use foo\baz\buzz;

class A {
    public function b(\foo\baz\buzz $buzz): void {
    }
}',
        ];
    }

    /**
     * @dataProvider provideCodeWithReturnTypesCases
     * @dataProvider provideCodeWithReturnTypesCasesWithNullableCases
     */
    public function testCodeWithReturnTypes(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideCodeWithReturnTypesCases(): iterable
    {
        yield 'Import common strict types' => [
            '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(Bar $foo): Baz
    {
    }
}',
            '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo): \Foo\Bar\Baz
    {
    }
}',
        ];

        yield 'Test namespace fixes' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $bar6): Baz
    {
    }
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $bar6): \Foo\Bar\Baz
    {
    }
}',
        ];

        yield 'Partial class name looks like FQCN' => [
            '<?php

namespace One;

use Two\Three;

class Two
{
    /**
     * Note that for this example, the following classes exist:
     *
     * - One\Two
     * - One\Two\Three
     * - Two\Three\Four
     */
    public function three(Three\Four $four): Two\Three
    {
    }
}',
        ];

        yield 'Test multi namespace fixes' => [
            '<?php
namespace Foo\Other {
}

namespace Foo\Bar {
    use Foo\Bar\Baz;

    class SomeClass
    {
        public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $bar5): Baz
        {
        }
    }
}',
            '<?php
namespace Foo\Other {
}

namespace Foo\Bar {
    use Foo\Bar\Baz;

    class SomeClass
    {
        public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $bar5): Baz
        {
        }
    }
}',
        ];

        yield 'Test fixes in interface' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $bar4): Baz;
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $bar4): \Foo\Bar\Baz;
}',
        ];

        yield 'Test fixes in trait' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

trait SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $bar3): Baz
    {
    }
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

trait SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $bar3): \Foo\Bar\Baz
    {
    }
}',
        ];

        yield 'Test fixes in regular functions' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $bar2): Baz
{
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $bar2): \Foo\Bar\Baz
{
}',
        ];

        yield 'Import common strict types with reserved' => [
            '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(Bar $foo, array $bar): Baz
    {
    }
}',
            '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo, array $bar): \Foo\Bar\Baz
    {
    }
}',
        ];
    }

    /**
     * @dataProvider provideCodeWithoutReturnTypesCases
     */
    public function testCodeWithoutReturnTypes(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideCodeWithoutReturnTypesCases(): iterable
    {
        yield 'import from namespace and global' => [
            '<?php
use App\DateTime;

class TestBar
{
    public function bar(\DateTime $dateTime)
    {
    }
}
',
        ];

        yield 'Import common strict types' => [
            '<?php

use Foo\Bar;

class SomeClass
{
    public function doSomething(Bar $foo)
    {
    }
}',
            '<?php

use Foo\Bar;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo)
    {
    }
}',
        ];

        yield 'Test namespace fixes' => [
            '<?php

namespace Foo\Bar;

class SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $bar1)
    {
    }
}',
            '<?php

namespace Foo\Bar;

class SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $bar1)
    {
    }
}',
        ];

        yield 'Partial class name looks like FQCN' => [
            '<?php

namespace One;

use Two\Three;

class Two
{
    /**
     * Note that for this example, the following classes exist:
     *
     * - One\Two
     * - One\Two\Three
     * - Two\Three
     */
    public function three(Two\Three $three, Three $other)
    {
    }
}',
        ];

        yield 'Test multi namespace fixes' => [
            '<?php
namespace Foo\Other {
}

namespace Foo\Bar {
    class SomeClass
    {
        public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz)
        {
        }
    }
}',
            '<?php
namespace Foo\Other {
}

namespace Foo\Bar {
    class SomeClass
    {
        public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
        {
        }
    }
}',
        ];

        yield 'Test fixes in interface' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz);
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz);
}',
        ];

        yield 'Test fixes in trait' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

trait SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz)
    {
    }
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

trait SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
    {
    }
}',
        ];

        yield 'Test fixes in regular functions' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz)
{
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
{
}',
        ];

        yield 'Test partial namespace and use imports' => [
            '<?php

namespace Ping\Pong;

use Foo\Bar;
use Ping;
use Ping\Pong\Pang;
use Ping\Pong\Pyng\Pung;

class SomeClass
{
    public function doSomething(
        \Ping\Something $something,
        Pung\Pang $other,
        \Ping\Pong\Pung $other1,
        Pang\Pung $other2,
        Pyng\Pung\Pong $other3,
        \Foo\Bar\Baz\Buz $other4
    ){}
}',
            '<?php

namespace Ping\Pong;

use Foo\Bar;
use Ping;
use Ping\Pong\Pang;
use Ping\Pong\Pyng\Pung;

class SomeClass
{
    public function doSomething(
        \Ping\Something $something,
        \Ping\Pong\Pung\Pang $other,
        \Ping\Pong\Pung $other1,
        \Ping\Pong\Pang\Pung $other2,
        \Ping\Pong\Pyng\Pung\Pong $other3,
        \Foo\Bar\Baz\Buz $other4
    ){}
}',
        ];

        yield 'Test reference' => [
            '<?php
function withReference(Exception &$e) {}',
            '<?php
function withReference(\Exception &$e) {}',
        ];

        yield 'Test reference with use' => [
            '<?php
use A\exception;
function withReference(\Exception &$e) {}',
        ];

        yield 'Test reference with use different casing' => [
            '<?php
namespace {
    use A\EXCEPTION;
    function withReference(\Exception &$e) {}
    }
',
        ];

        yield 'Test FQCN is not removed when class with the same name, but different namespace, is imported' => [
            '<?php namespace Foo;
                use Bar\TheClass;
                class Test
                {
                    public function __construct(
                        \Foo\TheClass $x
                    ) {}
                }
            ',
        ];
    }

    public static function provideCodeWithReturnTypesCasesWithNullableCases(): iterable
    {
        yield 'Test namespace fixes with nullable types' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, ?Zoof\Buz $barbuz): ?Baz
    {
    }
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, ?\Foo\Bar\Zoof\Buz $barbuz): ?\Foo\Bar\Baz
    {
    }
}',
        ];

        yield 'Partial class name looks like FQCN' => [
            '<?php

namespace One;

use Two\Three;

class Two
{
    /**
     * Note that for this example, the following classes exist:
     *
     * - One\Two
     * - One\Two\Three
     * - Two\Three\Four
     */
    public function three(Three\Four $four): ?Two\Three
    {
    }
}',
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php function foo(int|float $x) {}',
        ];

        yield [
            '<?php function foo(int|A $x) {}',
        ];

        yield [
            '<?php function foo(A|B|C $x) {}',
            '<?php function foo(\A|\B|\C $x) {}',
        ];

        yield [
            '<?php function foo(): A|B|C {}',
            '<?php function foo(): \A|\B|\C {}',
        ];

        yield 'aaa' => [
            '<?php function foo(): A | B | C {}',
            '<?php function foo(): \A | \B | \C {}',
        ];

        yield [
            '<?php function f(): Foo|Bar|A\B\C {}',
            '<?php function f(): Foo|\Bar|\A\B\C {}',
        ];
    }

    /**
     * @param array<string, bool> $config
     *
     * @requires PHP 8.1
     *
     * @dataProvider provideFix81Cases
     */
    public function testFix81(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php function f(): Foo&Bar & A\B\C {}',
            '<?php function f(): Foo&\Bar & \A\B\C {}',
        ];

        yield 'union/intersect param in global namespace without use' => [
            '<?php
function foo(\X|\Y $a, \X&\Y $b) {}
function bar(\X|\Y $a, \X&\Y $b) {}',
            '<?php
function foo(\X|\Y $a, \X&\Y $b) {}
function bar(X|Y $a, X&Y $b) {}',
            ['leading_backslash_in_global_namespace' => true],
        ];

        yield [
            '<?php
use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(Bar $foo): Foo\Bar\Ba3{}
    public function doSomethingMore(Bar|B $foo): Baz{}
    public function doSomethingElse(Bar&A\Z $foo): Baz{}
}',
            '<?php
use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo): \Foo\Bar\Ba3{}
    public function doSomethingMore(\Foo\Bar|B $foo): \Foo\Bar\Baz{}
    public function doSomethingElse(\Foo\Bar&\A\Z $foo): \Foo\Bar\Baz{}
}',
        ];
    }

    /**
     * @param array<string, bool> $config
     *
     * @requires PHP 8.2
     *
     * @dataProvider provideFix82Cases
     */
    public function testFix82(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield 'simple param in global namespace without use' => [
            '<?php
function foo(\X $x, \Y $y, int $z) {}
function bar(\X $x, \Y $y, true $z) {}',
            '<?php
function foo(\X $x, \Y $y, int $z) {}
function bar(X $x, Y $y, true $z) {}',
            ['leading_backslash_in_global_namespace' => true],
        ];

        yield 'simple return in global namespace without use' => [
            '<?php
function foo(): \X {}
function bar(): \Y {}
function x(): never {}',
            '<?php
function foo(): \X {}
function bar(): Y {}
function x(): never {}',
            ['leading_backslash_in_global_namespace' => true],
        ];

        yield [
            '<?php function foo((A&B)|(x&y&Ze)|int|null $x) {}',
            '<?php function foo((\A&\B)|(\x&\y&\Ze)|int|null $x) {}',
        ];
    }
}
