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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input, array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
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
            '<?php use B\Exception; function foo(Exception $e, \Exception $e2, A\B $f) {}',
            '<?php use B\Exception; function foo(\B\Exception $e, \Exception $e2, \A\B $f) {}',
            ['shorten_globals_in_global_ns' => true],
        ];

        yield 'simple use as' => [
            '<?php use D\Exception as C; function foo(C $e) {}',
            '<?php use D\Exception as C; function foo(\D\Exception $e) {}',
        ];

        yield 'simple use as casing' => [
            '<?php use E\Exception as C; function foo(C $e) {}',
            '<?php use E\Exception as C; function foo(\E\EXCEPTION $e) {}',
        ];

        yield 'simple use 2' => [
            '<?php use \F\Exception; function foo(Exception $e) {}',
            '<?php use \F\Exception; function foo(\F\Exception $e) {}',
        ];

        yield 'interface multiple extends' => [
            '<?php
namespace Foo\Bar;
use D\E;
use IIII\G;
use Foo\Bar\C;
interface NakanoInterface extends IzumiInterface, A, E, \C, EZ
{
}',
            '<?php
namespace Foo\Bar;
use D\E;
use IIII\G;
use Foo\Bar\C;
interface NakanoInterface extends \Foo\Bar\IzumiInterface, \Foo\Bar\A, \D\E, \C, EZ
{
}',
        ];

        yield 'interface in global namespace with global extend' => [
            '<?php interface Foo1 extends ArrayAccess2{}',
            '<?php interface Foo1 extends \ArrayAccess2{}',
            ['shorten_globals_in_global_ns' => true],
        ];

        yield 'interface in global namespace with multiple extend' => [
            '<?php use B\Exception; interface Foo extends ArrayAccess, \Exception, Exception {}',
            '<?php use B\Exception; interface Foo extends \ArrayAccess, \Exception, \B\Exception {}',
            ['shorten_globals_in_global_ns' => true],
        ];

        yield 'class implements' => [
            '<?php
namespace Foo\Bar;
class SomeClass implements Izumi
{
}',
            '<?php
namespace Foo\Bar;
class SomeClass implements \Foo\Bar\Izumi
{
}',
        ];

        yield 'class extends and implements' => [
            '<?php
namespace Foo\Bar;
class SomeClass extends A implements Izumi
{
}',
            '<?php
namespace Foo\Bar;
class SomeClass extends \Foo\Bar\A implements \Foo\Bar\Izumi
{
}',
        ];

        yield 'class extends and implements multiple' => [
            '<?php
namespace Foo\Bar;
class SomeClass extends A implements Izumi, A, \A\B, C
{
}',
            '<?php
namespace Foo\Bar;
class SomeClass extends \Foo\Bar\A implements \Foo\Bar\Izumi, A, \A\B, \Foo\Bar\C
{
}',
        ];

        yield 'single caught exception' => [
            '<?php use A\B; echo 1; try{ foo(999); } catch (B $z) {}',
            '<?php use A\B; echo 1; try{ foo(999); } catch (\A\B $z) {}',
        ];

        yield 'single caught exception namespaced' => [
            '<?php namespace B; try{ foo(999); } catch (A $z) {}',
            '<?php namespace B; try{ foo(999); } catch (\B\A $z) {}',
        ];

        yield 'multiple caught exceptions' => [
            '<?php namespace D; use A\B; try{ foo(); } catch (B |  \A\C  | /* 1 */  \A\D $z) {}',
            '<?php namespace D; use A\B; try{ foo(); } catch (\A\B |  \A\C  | /* 1 */  \A\D $z) {}',
        ];

        yield 'catch in multiple namespaces' => [
            '<?php
namespace {
    try{ foo(); } catch (Exception $z) {}
    try{ foo(); } catch (A\X $z) {}
    try{ foo(); } catch (B\Z $z) {}
}

namespace A {
    try{ foo(); } catch (\Exception $z) {}
    try{ foo(); } catch (X $z) {}
    try{ foo(); } catch (\B\Z $z) {}
}

namespace B {
    try{ foo(); } catch (\Exception $z) {}
    try{ foo(); } catch (\A\X $z) {}
    try{ foo(); } catch (Z $z) {}
}
',
            '<?php
namespace {
    try{ foo(); } catch (\Exception $z) {}
    try{ foo(); } catch (\A\X $z) {}
    try{ foo(); } catch (\B\Z $z) {}
}

namespace A {
    try{ foo(); } catch (\Exception $z) {}
    try{ foo(); } catch (\A\X $z) {}
    try{ foo(); } catch (\B\Z $z) {}
}

namespace B {
    try{ foo(); } catch (\Exception $z) {}
    try{ foo(); } catch (\A\X $z) {}
    try{ foo(); } catch (\B\Z $z) {}
}
',
            ['shorten_globals_in_global_ns' => true],
        ];

        yield 'starts with but not full name arg' => [
            '<?php
            use XYZ\A;

            function Foo(\XYZ\AT $f) {

            }
            ',
            null,
        ];

        yield 'starts with but not full name extends' => [
            '<?php namespace a\abcd;
class Foo extends \a\abcdTest { }',
            null,
        ];

        yield 'starts with but not full name function arg' => [
            '<?php
namespace Z\B\C\D
{
    function A(\Z\B\C\DE\Foo $fix) {}
}
',
            null,
        ];

        yield 'static class reference' => [
            '<?php
            use ZXY\A;

            echo A::class;
            echo A::B();
            echo A::class;
            foo(A::B,A::C);
            echo $a[A::class];
            echo A::class?>
            ',
            '<?php
            use ZXY\A;

            echo \ZXY\A::class;
            echo \ZXY\A::B();
            echo \ZXY\A::class;
            foo(\ZXY\A::B,\ZXY\A::C);
            echo $a[\ZXY\A::class];
            echo \ZXY\A::class?>
            ',
        ];

        yield [
            '<?php
            namespace Foo\Test;
            $this->assertSame($names, \Foo\TestMyThing::zxy(1,2));
            ',
            null,
        ];

        yield [
            '<?php
            use ZXY\A;
            use D;

            echo $D::CONST_VALUE;
            echo parent::CONST_VALUE;
            echo self::$abc;
            echo Z::F;
            echo X\Z::F;
            ',
            null,
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
    public function testCodeWithoutReturnTypes(string $expected, ?string $input = null, array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

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
            ['shorten_globals_in_global_ns' => true],
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
        $this->fixer->configure(['shorten_globals_in_global_ns' => true]);
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

        yield 'caught exception without var' => [
            '<?php use A\B; try{ foo(0); } catch (B) {}',
            '<?php use A\B; try{ foo(0); } catch (\A\B) {}',
        ];
    }

    /**
     * @requires PHP 8.1
     *
     * @dataProvider provideFix81Cases
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->fixer->configure(['shorten_globals_in_global_ns' => true]);
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
