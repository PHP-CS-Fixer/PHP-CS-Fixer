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
use PhpCsFixer\WhitespacesFixerConfig;

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
    public function testFix(
        string $expected,
        ?string $input = null,
        array $config = [],
        ?WhitespacesFixerConfig $whitespaceConfig = null
    ): void {
        $this->fixer->configure($config);

        if (null !== $whitespaceConfig) {
            $this->fixer->setWhitespacesConfig($whitespaceConfig);
            $expected = str_replace("\n", $whitespaceConfig->getLineEnding(), $expected);
            if (null !== $input) {
                $input = str_replace("\n", $whitespaceConfig->getLineEnding(), $input);
            }
        }

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
            '<?php interface Foo1 extends \ArrayAccess2{}',
            '<?php interface Foo1 extends ArrayAccess2{}',
            ['leading_backslash_in_global_namespace' => true],
        ];

        yield 'interface in global namespace with multiple extend' => [
            '<?php use B\Exception; interface Foo extends \ArrayAccess, \Exception, Exception {}',
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
    try{ foo(); } catch (\Exception $z) {}
    try{ foo(); } catch (\Exception $z) {}
    try{ foo(); } catch (\A\X $z) {}
    try{ foo(); } catch (\A\X $z) {}
    try{ foo(); } catch (\B\Z $z) {}
    try{ foo(); } catch (\B\Z $z) {}
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
    try{ foo(); } catch (Exception $z) {}
    try{ foo(); } catch (\Exception $z) {}
    try{ foo(); } catch (A\X $z) {}
    try{ foo(); } catch (\A\X $z) {}
    try{ foo(); } catch (B\Z $z) {}
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

        yield 'new class' => [
            '<?php use A\B; new B();',
            '<?php use A\B; new \A\B();',
        ];

        yield 'new class namespaced' => [
            '<?php namespace B; new A();',
            '<?php namespace B; new \B\A();',
        ];

        yield 'new class not imported' => [
            '<?php new A\B(); new A\B();',
            '<?php new \A\B(); new A\B();',
        ];

        yield 'instanceof' => [
            '<?php use A\B; $res = $v instanceof B;',
            '<?php use A\B; $res = $v instanceof \A\B;',
        ];

        yield 'instanceof namespaced' => [
            '<?php namespace B; $res = ($v->obj()) instanceof A;',
            '<?php namespace B; $res = ($v->obj()) instanceof \B\A;',
        ];

        yield 'use trait simple' => [
            '<?php use A\B; class Foo { use B; };',
            '<?php use A\B; class Foo { use \A\B; };',
        ];

        yield 'use trait complex' => [
            '<?php use A\B; class Foo { use A\C; use D; use B { B::bar as baz; } };',
            '<?php use A\B; class Foo { use \A\C; use \D; use \A\B { \A\B::bar as baz; } };',
        ];

        yield 'typed property in class' => [
            '<?php use A\B; class Cl { public B $p; var B $p2; }',
            '<?php use A\B; class Cl { public \A\B $p; var \A\B $p2; }',
        ];

        yield 'typed property in anonymous class' => [
            '<?php use A\B; new class() { public B $p; };',
            '<?php use A\B; new class() { public \A\B $p; };',
        ];

        yield 'typed nullable property in class' => [
            '<?php use A\B; class Cl { public ?B $p = null, $r; }',
            '<?php use A\B; class Cl { public ?\A\B $p = null, $r; }',
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

        yield 'import new symbols from all supported places' => [
            '<?php

namespace Foo\Test;
use Other\BaseClass;
use Other\CaughtThrowable;
use Other\FunctionArgument;
use Other\FunctionReturnType;
use Other\InstanceOfClass;
use Other\Interface1;
use Other\Interface2;
use Other\NewClass;
use Other\PropertyPhpDoc;
use Other\StaticFunctionCall;

class Foo extends BaseClass implements Interface1, Interface2
{
    /** @var PropertyPhpDoc */
    private $array;
    public function __construct(FunctionArgument $arg) {}
    public function foo(): FunctionReturnType
    {
        try {
            StaticFunctionCall::bar();
        } catch (CaughtThrowable $e) {}
    }
}

new NewClass();

if ($a instanceof InstanceOfClass) { return false; }
            ',
            '<?php

namespace Foo\Test;

class Foo extends \Other\BaseClass implements \Other\Interface1, \Other\Interface2
{
    /** @var \Other\PropertyPhpDoc */
    private $array;
    public function __construct(\Other\FunctionArgument $arg) {}
    public function foo(): \Other\FunctionReturnType
    {
        try {
            \Other\StaticFunctionCall::bar();
        } catch (\Other\CaughtThrowable $e) {}
    }
}

new \Other\NewClass();

if ($a instanceof \Other\InstanceOfClass) { return false; }
            ',
            ['import_symbols' => true],
        ];

        yield 'import new symbols under already existing imports' => [
            '<?php

namespace Foo\Test;

use Other\A;
use Other\B;
use Other\C;
use Other\D;
use Other\E;

function foo(A $a, B $b) {}
function bar(C $c, D $d): E {}
',
            '<?php

namespace Foo\Test;

use Other\A;
use Other\B;

function foo(A $a, B $b) {}
function bar(\Other\C $c, \Other\D $d): \Other\E {}
',
            ['import_symbols' => true],
        ];

        yield 'import new symbols within multiple namespaces' => [
            '<?php

namespace Foo\Bar {
    use Other\A;
use Other\B;

    function foo(A $a, B $b) {}
}
namespace Foo\Baz {
    use Other\A;
use Other\C;

    function foo(A $a, C $c) {}
}
',
            '<?php

namespace Foo\Bar {
    use Other\A;

    function foo(A $a, \Other\B $b) {}
}
namespace Foo\Baz {
    use Other\A;

    function foo(A $a, \Other\C $c) {}
}
',
            ['import_symbols' => true],
        ];

        yield 'import new symbols with no existing imports nor namespace /wo declare' => [
            <<<'EOD'
                <?php

                use Ns\A;
                // comment

                foo();

                function foo(A $v) {}
                EOD,
            <<<'EOD'
                <?php

                // comment

                foo();

                function foo(\Ns\A $v) {}
                EOD,
            ['import_symbols' => true],
        ];

        yield 'import new symbols with no existing imports nor namespace /w declare' => [
            <<<'EOD'
                <?php

                // comment

                declare(strict_types=1);
                use Ns\A;

                function foo(A $v) {}
                EOD,
            <<<'EOD'
                <?php

                // comment

                declare(strict_types=1);

                function foo(\Ns\A $v) {}
                EOD,
            ['import_symbols' => true],
        ];

        yield 'import new symbols with custom whitespace config' => [
            '<?php

namespace Foo\Bar;

use Other\A;
use Other\B;

function foo(A $a, B $b) {}
',
            '<?php

namespace Foo\Bar;

use Other\A;

function foo(A $a, \Other\B $b) {}
',
            ['import_symbols' => true],
            new WhitespacesFixerConfig("\t", "\r\n"),
        ];

        yield 'ignore importing if there is name conflict' => [
            '<?php namespace Foo\Test; use Other\A; function foo(A $a, \YetAnother\A $b) {}',
            null,
            ['import_symbols' => true],
        ];

        yield 'ignore importing if symbol is not a FQN' => [
            '<?php namespace Foo\Test; use Foo\Test\Sub\Symbol1; function foo(Symbol1 $a, Sub\Symbol2 $b) {}',
            null,
            ['import_symbols' => true],
        ];

        yield 'ignore global FQNs (there is GlobalNamespaceImportFixer for that)' => [
            '<?php namespace Foo\Test; function foo(\Symbol $a, \OtherSymbol $b) {}',
            null,
            ['import_symbols' => true],
        ];

        yield '@link shall not crash fixer' => [
            '<?php

use Symfony\Component\Validator\Constraints\Valid;
/**
 * {@link Valid} is assumed.
 *
 * @return void
 */
function validate(): void {}
',
            '<?php

/**
 * {@link \Symfony\Component\Validator\Constraints\Valid} is assumed.
 *
 * @return void
 */
function validate(): void {}
',
            ['import_symbols' => true, 'phpdoc_tags' => ['link']],
        ];

        yield 'import short name only once (ignore consequent same-name, different-namespace symbols)' => [
            '<?php

namespace Test;
use A\A;

class Foo extends A implements \B\A, \C\A
{
    /** @var \D\A */
    private $array;
    public function __construct(\E\A $arg) {}
    public function foo(): \F\A
    {
        try {
            \G\A::bar();
        } catch (\H\A $e) {}
    }
}',
            '<?php

namespace Test;

class Foo extends \A\A implements \B\A, \C\A
{
    /** @var \D\A */
    private $array;
    public function __construct(\E\A $arg) {}
    public function foo(): \F\A
    {
        try {
            \G\A::bar();
        } catch (\H\A $e) {}
    }
}',
            ['import_symbols' => true],
        ];

        yield 'import only if not already implicitly used by class declaration' => [
            <<<'EOD'
                <?php

                namespace Ns;

                class City
                {
                    public \Ns2\City $city;
                }
                EOD,
            null,
            ['import_symbols' => true],
        ];

        yield 'import only if not already implicitly used by interface declaration' => [
            <<<'EOD'
                <?php

                namespace Ns;

                interface City
                {
                    public function f(\Ns2\City $city);
                }
                EOD,
            null,
            ['import_symbols' => true],
        ];

        yield 'import only if not already implicitly used by trait declaration' => [
            <<<'EOD'
                <?php

                namespace Ns;

                trait City
                {
                    public \Ns2\City $city;
                }
                EOD,
            null,
            ['import_symbols' => true],
        ];

        yield 'import only if not already implicitly used by short name usage in class instantiation' => [
            <<<'EOD'
                <?php

                namespace Ns;

                new \Ns2\MyCl();
                new MyCl();
                EOD,
            null,
            ['import_symbols' => true],
        ];

        yield 'import only if not already implicitly used by short name usage in attribute' => [
            <<<'EOD'
                <?php

                namespace Ns;

                new \Ns2\MyCl();
                #[MyCl]
                class Cl {}
                EOD,
            null,
            ['import_symbols' => true],
        ];

        yield 'import only if not already implicitly used by short name usage in phpdoc' => [
            <<<'EOD'
                <?php

                namespace Ns;

                new \Ns2\MyCl();
                /** @var MyCl */;
                EOD,
            null,
            ['import_symbols' => true],
        ];

        yield 'import with relative and absolute symbols - global' => [
            <<<'EOD'
                <?php

                use Foo\Bar;
                new Exception();
                new Exception();
                new Bar();
                EOD,
            <<<'EOD'
                <?php

                new \Exception();
                new Exception();
                new Foo\Bar();
                EOD,
            ['import_symbols' => true],
        ];

        yield 'import with relative and absolute symbols - global and leading backslash' => [
            <<<'EOD'
                <?php

                use Foo\Bar;
                new \Exception();
                new \Exception();
                new Bar();
                EOD,
            <<<'EOD'
                <?php

                new \Exception();
                new Exception();
                new Foo\Bar();
                EOD,
            ['import_symbols' => true, 'leading_backslash_in_global_namespace' => true],
        ];

        yield 'import with relative and absolute symbols - namespaced' => [
            <<<'EOD'
                <?php

                namespace Ns;
                use Ns2\Foo4;
                use Ns\Foo3\Sub3;
                use Ns\Foo\Sub2;

                new Foo();
                new Foo\Sub();
                new Foo();
                new Foo2();
                new Sub2();
                new Sub3();
                new \Ns2\Foo();
                new Foo4();
                EOD,
            <<<'EOD'
                <?php

                namespace Ns;

                new Foo();
                new Foo\Sub();
                new \Ns\Foo();
                new \Ns\Foo2();
                new \Ns\Foo\Sub2();
                new \Ns\Foo3\Sub3();
                new \Ns2\Foo();
                new \Ns2\Foo4();
                EOD,
            ['import_symbols' => true],
        ];

        yield 'shorten relative reference to already imported, direct short name' => [
            <<<'EOD'
                <?php
                namespace Foo\Bar\Baz;

                use Foo\Bar;
                use Foo\Bar\A\B;

                final class Buzz extends Bar implements B {}
                final class Fuzz extends Bar implements B {}
                EOD,
            <<<'EOD'
                <?php
                namespace Foo\Bar\Baz;

                use Foo\Bar;
                use Foo\Bar\A\B;

                final class Buzz extends Bar implements Bar\A\B {}
                final class Fuzz extends Bar implements B {}
                EOD,
        ];

        yield 'fix to longest imported name' => [
            <<<'EOD'
                <?php

                use A\B;
                use A\X as Y;
                use S as R;
                use S\T;

                new B();
                new B\C();
                new Y();
                new Y\Z();
                new T();
                EOD,
            <<<'EOD'
                <?php

                use A\B;
                use A\X as Y;
                use S as R;
                use S\T;

                new \A\B();
                new \A\B\C();
                new \A\X();
                new \A\X\Z();
                new R\T();
                EOD,
        ];

        yield 'shortening - namespace with shorter import' => [
            <<<'EOD'
                <?php
                namespace U\V\W;
                use U\V;
                new \U();
                new V();
                new V\W();
                new X();
                new X\Y();
                new X\Y\Z();
                EOD,
        ];

        yield 'shortening - namespace with same import' => [
            <<<'EOD'
                <?php
                namespace U\V\W;
                use U\V\W;
                new \U();
                new \U\V();
                new W();
                new X();
                new X\Y();
                new X\Y\Z();
                EOD,
        ];

        yield 'shortening - namespace with useless import' => [
            <<<'EOD'
                <?php
                namespace U\V\W;
                use U\V\W\X;
                new \U();
                new \U\V();
                new \U\V\W();
                new X();
                new X\Y();
                new X\Y\Z();
                EOD,
        ];

        yield 'shortening - namespace with longer import' => [
            <<<'EOD'
                <?php
                namespace U\V\W;
                use U\V\W\X\Y;
                new \U();
                new \U\V();
                new \U\V\W();
                new X();
                new Y();
                new Y\Z();
                new Y\Z\e();
                new Y\Z\e\f();
                EOD,
        ];

        yield 'do not fix class named the same as imported function' => [
            <<<'EOD'
                <?php
                namespace Foo;
                use Bar\Request;
                use function Baz\request;
                class Test
                {
                    public function request(Request $request = null)
                    {
                        $request = $request ?? Request::create('/docs.json');
                    }
                }
                $request = new Request();
                EOD,
        ];

        yield 'do not fix property named the same as class' => [
            <<<'EOD'
                <?php
                namespace Foo;
                use Bar\Service;
                class Baz {
                    public function getValue()
                    {
                        return $this->service::getValueFromService();
                    }

                }
                EOD,
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
        Ping\Something $something,
        Ping\Pong\Pung\Pang $other,
        Ping\Pong\Pung $other1,
        Pang\Pung $other2,
        Pung\Pong $other3,
        Bar\Baz\Buz $other4
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
     * @return iterable<string, array{0: string, 1?: null|string, 2?: array<string, mixed>}>
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

        yield 'Test PHPDoc union' => [
            '<?php

namespace Ns;

/**
 * @param \Exception|\Exception2|int|null $v
 */
function foo($v) {}',
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

        yield 'PHPDoc with generics must not crash' => [
            '<?php

/**
 * @param \Iterator<mixed, \SplFileInfo> $iter
 */
function foo($iter) {}',
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
 *
 * @property SomeClass $foo
 * @property-read Buz $buz
 * @property-write Baz $baz
 * @phpstan-property SomeClass $foo
 * @phpstan-property-read Buz $buz
 * @phpstan-property-write Baz $baz
 * @psalm-property SomeClass $foo
 * @psalm-property-read Buz $buz
 * @psalm-property-write Baz $baz
 */
interface SomeClass
{
    /**
    * @param SomeClass $foo
    * @phpstan-param Buz $buz
    *
    * @psalm-return Baz
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
 *
 * @property \Foo\Bar\SomeClass $foo
 * @property-read \Foo\Bar\Buz $buz
 * @property-write \Foo\Bar\Baz $baz
 * @phpstan-property \Foo\Bar\SomeClass $foo
 * @phpstan-property-read \Foo\Bar\Buz $buz
 * @phpstan-property-write \Foo\Bar\Baz $baz
 * @psalm-property \Foo\Bar\SomeClass $foo
 * @psalm-property-read \Foo\Bar\Buz $buz
 * @psalm-property-write \Foo\Bar\Baz $baz
 */
interface SomeClass
{
    /**
    * @param \Foo\Bar\SomeClass $foo
    * @phpstan-param \Foo\Bar\Buz $buz
    *
    * @psalm-return \Foo\Bar\Baz
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

        yield 'Leading backslash in global namespace - standard phpdoc' => [
            '<?php

/**
 * @param \DateTimeInterface $dateTime
 * @param callable(): (\Closure(): void) $fx
 * @return \DateTimeInterface
 * @see \DateTimeImmutable
 * @throws \Exception
 */
function foo($dateTime, $fx) {}',
            '<?php

/**
 * @param DateTimeInterface $dateTime
 * @param callable(): (\Closure(): void) $fx
 * @return DateTimeInterface
 * @see DateTimeImmutable
 * @throws Exception
 */
function foo($dateTime, $fx) {}',
            ['leading_backslash_in_global_namespace' => true],
        ];

        yield 'Leading backslash in global namespace - reserved phpdoc' => [
            '<?php

/**
 * @param int $v
 * @phpstan-param positive-int $v
 * @param \'GET\'|\'POST\' $method
 * @param \Closure $fx
 * @psalm-param Closure(): (callable(): Closure) $fx
 * @return list<int>
 */
function foo($v, $method, $fx) {}',
            '<?php

/**
 * @param int $v
 * @phpstan-param positive-int $v
 * @param \'GET\'|\'POST\' $method
 * @param Closure $fx
 * @psalm-param Closure(): (callable(): Closure) $fx
 * @return list<int>
 */
function foo($v, $method, $fx) {}',
            ['leading_backslash_in_global_namespace' => true],
        ];

        yield 'Do not touch PHPDoc if configured with empty collection' => [
            '<?php

namespace Foo\Bar;

use Foo\Bar\Buz;
use Foo\Bar\Baz;
use Foo\Bar\SomeClass;

/**
 * @see \Foo\Bar\Baz
 * @see \Foo\Bar\Bam
 *
 * @property \Foo\Bar\SomeClass $foo
 * @property-read \Foo\Bar\Buz $buz
 * @property-write \Foo\Bar\Baz $baz
 */
interface SomeClass
{
    /**
    * @param \Foo\Bar\SomeClass $foo
    * @phpstan-param \Foo\Bar\Buz $buz
    *
    * @psalm-return \Foo\Bar\Baz
    */
    public function doSomething($foo, $buz);
}',
            null,
            ['phpdoc_tags' => []],
        ];

        yield 'Process only specified PHPDoc annotation' => [
            '<?php

namespace Foo\Bar;

use Foo\Baz\Buzz;

/**
 * @see \Foo\Baz\Buzz
 *
 * @property \Foo\Baz\Buzz $buzz1
 * @property-read Buzz $buzz2
 */
interface SomeClass
{
    /**
    * @param \Foo\Baz\Buzz $a
    * @phpstan-param Buzz $b
    *
    * @psalm-return \Foo\Baz\Buzz
    */
    public function doSomething($a, $b): void;
}',
            '<?php

namespace Foo\Bar;

use Foo\Baz\Buzz;

/**
 * @see \Foo\Baz\Buzz
 *
 * @property \Foo\Baz\Buzz $buzz1
 * @property-read \Foo\Baz\Buzz $buzz2
 */
interface SomeClass
{
    /**
    * @param \Foo\Baz\Buzz $a
    * @phpstan-param \Foo\Baz\Buzz $b
    *
    * @psalm-return \Foo\Baz\Buzz
    */
    public function doSomething($a, $b): void;
}',
            ['phpdoc_tags' => ['property-read', 'phpstan-param']],
        ];

        yield 'ignore @see with URL' => [
            '<?php
/**
 * @see     http://example.com
 */
define(\'FOO_BAR\', true);',
        ];

        yield 'Respect whitespace between phpDoc annotation and value' => [
            '<?php

namespace Foo\Test;

use Foo\Bar;

/**
 * @param Bar $a
 * @see   Bar
 */
function foo($a) {}',
            '<?php

namespace Foo\Test;

use Foo\Bar;

/**
 * @param \Foo\Bar $a
 * @see   \Foo\Bar
 */
function foo($a) {}',
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

        yield 'typed promoted property in class' => [
            '<?php use A\B; class Cl { public function __construct(private B $p2) {} }',
            '<?php use A\B; class Cl { public function __construct(private \A\B $p2) {} }',
        ];

        yield 'import new symbols from attributes' => [
            '<?php

namespace Foo\Test;
use Other\ClassAttr;
use Other\MethodAttr;
use Other\PromotedAttr;
use Other\PropertyAttr;

#[ClassAttr]
#[\AllowDynamicProperties]
class Foo
{
    #[PropertyAttr]
    public int $prop;

    public function __construct(
        #[PromotedAttr]
        public int $arg
    ) {}

    #[MethodAttr]
    public function foo(): void {}
}
            ',
            '<?php

namespace Foo\Test;

#[\Other\ClassAttr]
#[\AllowDynamicProperties]
class Foo
{
    #[\Other\PropertyAttr]
    public int $prop;

    public function __construct(
        #[\Other\PromotedAttr]
        public int $arg
    ) {}

    #[\Other\MethodAttr]
    public function foo(): void {}
}
            ',
            ['import_symbols' => true],
        ];

        yield 'do not fix property named the same as class' => [
            <<<'EOD'
                <?php
                namespace Foo;
                use Bar\Baz;
                echo $x?->baz::CONSTANT_1;
                EOD,
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
    public function doSomething(Bar $foo): Bar\Ba3{}
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

        yield 'import only if not already implicitly used by enum declaration' => [
            <<<'EOD'
                <?php

                namespace Ns;

                enum City
                {
                    public function f(\Ns2\City $city) {}
                }
                EOD,
            null,
            ['import_symbols' => true],
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
