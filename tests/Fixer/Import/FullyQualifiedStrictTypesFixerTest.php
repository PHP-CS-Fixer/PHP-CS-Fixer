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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
    public static function provideFixCases(): iterable
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
            ['leading_backslash_in_global_namespace' => true],
        ];

        yield 'interface in global namespace with multiple extend' => [
            '<?php use B\Exception; interface Foo extends ArrayAccess, \Exception, Exception {}',
            '<?php use B\Exception; interface Foo extends \ArrayAccess, \Exception, \B\Exception {}',
            ['leading_backslash_in_global_namespace' => true],
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

        yield 'anonymous class implements, shorten to namespace' => [
            '<?php
namespace Foo\Bar;
$a = new class implements Izumi {};',
            '<?php
namespace Foo\Bar;
$a = new class implements \Foo\Bar\Izumi {};',
        ];

        yield 'anonymous class implements, shorten to imported name' => [
            '<?php
use Foo\Bar\Izumi;
$a = new class implements Izumi {};',
            '<?php
use Foo\Bar\Izumi;
$a = new class implements \Foo\Bar\Izumi {};',
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
            ['leading_backslash_in_global_namespace' => true],
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

    /**
     * @return iterable<array{0: string, 1?: null|string}>
     */
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
     * @param array<string, array<string, mixed>|bool> $config
     *
     * @dataProvider provideCodeWithoutReturnTypesCases
     */
    public function testCodeWithoutReturnTypes(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string}>
     */
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

    /**
     * @return iterable<array{0: string, 1?: null|string}>
     */
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
     * @param array<string, mixed> $config
     *
     * @dataProvider provideCodeWithPhpDocCases
     */
    public function testCodeWithPhpDoc(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string, 2?: array<string, mixed>}>
     */
    public static function provideCodeWithPhpDocCases(): iterable
    {
        yield 'Test class PHPDoc fixes' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;
use Foo\Bar\Bam;

/**
 * @see Baz
 * @see Bam
 */
class SomeClass
{
    /**
     * @var Baz
     */
    public $baz;

    /** @var Bam */
    public $bam;
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;
use Foo\Bar\Bam;

/**
 * @see \Foo\Bar\Baz
 * @see \Foo\Bar\Bam
 */
class SomeClass
{
    /**
     * @var \Foo\Bar\Baz
     */
    public $baz;

    /** @var \Foo\Bar\Bam */
    public $bam;
}',
        ];

        yield 'Test PHPDoc nullable fixes' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;
use Foo\Bar\Bam;

/**
 * @see Baz|null
 * @see Bam|null
 */
class SomeClass {}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;
use Foo\Bar\Bam;

/**
 * @see \Foo\Bar\Baz|null
 * @see \Foo\Bar\Bam|null
 */
class SomeClass {}',
        ];

        yield 'Test PHPDoc in interface' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
   /**
    * @param SomeClass $foo
    * @param Buz $buz
    * @param Zoof\Buz $barbuz
    *
    * @return Baz
    */
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz): Baz;
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
   /**
    * @param \Foo\Bar\SomeClass $foo
    * @param \Foo\Bar\Buz $buz
    * @param \Foo\Bar\Zoof\Buz $barbuz
    *
    * @return \Foo\Bar\Baz
    */
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): \Foo\Bar\Baz;
}',
        ];

        yield 'Test PHPDoc in interface with no imports' => [
            '<?php

namespace Foo\Bar;

interface SomeClass
{
   /**
    * @param SomeClass $foo
    * @param Buz $buz
    * @param Zoof\Buz $barbuz
    *
    * @return Baz
    */
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz): Baz;
}',
            '<?php

namespace Foo\Bar;

interface SomeClass
{
   /**
    * @param \Foo\Bar\SomeClass $foo
    * @param \Foo\Bar\Buz $buz
    * @param \Foo\Bar\Zoof\Buz $barbuz
    *
    * @return \Foo\Bar\Baz
    */
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): \Foo\Bar\Baz;
}',
        ];

        yield 'Test not imported PHPDoc fixes' => [
            '<?php

namespace Foo\Bar;

/**
 * @see Baz
 * @see Bam
 */
final class SomeClass {}',
            '<?php

namespace Foo\Bar;

/**
 * @see \Foo\Bar\Baz
 * @see \Foo\Bar\Bam
 */
final class SomeClass {}',
        ];

        yield 'Test multiple PHPDoc blocks' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Buz;
use Foo\Bar\Baz;
use Foo\Bar\SomeClass;

/**
 * @see Baz
 * @see Bam
 */
interface SomeClass
{
    /**
    * @param SomeClass $foo
    * @param Buz $buz
    *
    * @return Baz
    */
    public function doSomething(SomeClass $foo, Buz $buz): Baz;
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Buz;
use Foo\Bar\Baz;
use Foo\Bar\SomeClass;

/**
 * @see \Foo\Bar\Baz
 * @see \Foo\Bar\Bam
 */
interface SomeClass
{
    /**
    * @param \Foo\Bar\SomeClass $foo
    * @param \Foo\Bar\Buz $buz
    *
    * @return \Foo\Bar\Baz
    */
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz): \Foo\Bar\Baz;
}',
        ];

        yield 'Skip @covers in tests (they require FQCN)' => [
            '<?php

namespace Tests\Foo\Bar;

use Foo\Bar\SomeClass;

/**
 * @covers \Foo\Bar\SomeClass
 */
class SomeClassTest {}',
        ];

        yield 'Imports with aliases' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz as Buzz;
use Foo\Bar\Bam as Boom;

/**
 * @see Buzz
 * @see Boom
 */
class SomeClass
{
    /**
     * @var Buzz
     */
    public $baz;

    /** @var Boom */
    public $bam;

    /**
     * @param Buzz $baz
     * @param Boom $bam
     */
    public function __construct($baz, $bam) {
        $this->baz = $baz;
        $this->bam = $bam;
    }

    /**
     * @return Buzz
     */
    public function getBaz() {
        return $this->baz;
    }

    /**
     * @return Boom
     */
    public function getBam() {
        return $this->bam;
    }
}',
            '<?php

namespace Foo\Bar;

use Foo\Bar\Baz as Buzz;
use Foo\Bar\Bam as Boom;

/**
 * @see \Foo\Bar\Baz
 * @see \Foo\Bar\Bam
 */
class SomeClass
{
    /**
     * @var \Foo\Bar\Baz
     */
    public $baz;

    /** @var \Foo\Bar\Bam */
    public $bam;

    /**
     * @param \Foo\Bar\Baz $baz
     * @param \Foo\Bar\Bam $bam
     */
    public function __construct($baz, $bam) {
        $this->baz = $baz;
        $this->bam = $bam;
    }

    /**
     * @return \Foo\Bar\Baz
     */
    public function getBaz() {
        return $this->baz;
    }

    /**
     * @return \Foo\Bar\Bam
     */
    public function getBam() {
        return $this->bam;
    }
}',
        ];

        yield 'Leading backslash in global namespace' => [
            '<?php

/**
 * @param \DateTimeInterface $dateTime
 * @return \DateTimeInterface
 * @see \DateTimeImmutable
 * @throws \Exception
 */
function foo($dateTime) {}',
            '<?php

/**
 * @param DateTimeInterface $dateTime
 * @return DateTimeInterface
 * @see DateTimeImmutable
 * @throws Exception
 */
function foo($dateTime) {}',
            ['leading_backslash_in_global_namespace' => true],
        ];
    }

    /**
     * @param array<string, array<string, mixed>|bool> $config
     *
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string}>
     */
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

        yield 'caught exception without var' => [
            '<?php use A\B; try{ foo(0); } catch (B) {}',
            '<?php use A\B; try{ foo(0); } catch (\A\B) {}',
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

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
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

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
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
