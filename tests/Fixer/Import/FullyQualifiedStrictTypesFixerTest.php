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
     * @dataProvider provideNewLogicCases
     */
    public function testNewLogic(string $expected, ?string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideNewLogicCases(): iterable
    {
        yield 'namespace === type name' => [
            '<?php
namespace Foo\Bar;
function test(\Foo\Bar $x) {}',
            null,
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
    }

    /**
     * @dataProvider provideCodeWithReturnTypesCases
     * @dataProvider provideCodeWithReturnTypesCasesWithNullableCases
     */
    public function testCodeWithReturnTypes(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideCodeWithReturnTypesCases(): iterable
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

    public function provideCodeWithoutReturnTypesCases(): iterable
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
        Pung $other1,
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
    }

    public function provideCodeWithReturnTypesCasesWithNullableCases(): array
    {
        return [
            'Test namespace fixes with nullable types' => [
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
            ],
            'Partial class name looks like FQCN' => [
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
            ],
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
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
     * @requires PHP 8.1
     *
     * @dataProvider provideFix81Cases
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php function f(): Foo&Bar & A\B\C {}',
            '<?php function f(): Foo&\Bar & \A\B\C {}',
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
}
