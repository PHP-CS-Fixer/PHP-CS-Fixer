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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer
 */
final class VisibilityRequiredFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, string> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'properties' => [
            <<<'EOD'
                <?php
                class Foo {
                    public $var;
                    protected $var_foo;
                    private $FooBar;
                    public static $var1;
                    protected static $var_foo2;
                    private static $FooBar1;
                    public static $var2;
                    protected static $var_foo3;
                    private static $FooBar2;
                    private static $FooBar3;
                    public $old = 'foo';
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    public $var;
                    protected $var_foo;
                    private $FooBar;
                    static public $var1;
                    static protected $var_foo2;
                    static private $FooBar1;
                    public static $var2;
                    protected static $var_foo3;
                    private static $FooBar2;
                    private static
                    $FooBar3;
                    var $old = 'foo';
                }
                EOD,
        ];

        yield 'properties after method' => [
            <<<'EOD'
                <?php
                class Foo {
                    public function aaa() {}
                    public $bbb;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    public function aaa() {}
                    var $bbb;
                }
                EOD,
        ];

        yield 'methods' => [
            <<<'EOD'
                <?php
                class MyTestWithAnonymousClass extends TestCase
                {
                    public function setUp()
                    {
                        $provider = new class(function () {}) {};
                    }

                    public function testSomethingWithMoney(
                        Money $amount
                    ) {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class MyTestWithAnonymousClass extends TestCase
                {
                    function setUp()
                    {
                        $provider = new class(function () {}) {};
                    }

                    public function testSomethingWithMoney(
                        Money $amount
                    ) {
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                abstract class Foo {
                    public function& foo0() {}
                    public function & foo1() {}
                    public function &foo2() {}
                    protected function foo3($b) {}
                    abstract protected function foo4();
                    private function foo5() {}
                    final public function foo6() {}
                    abstract public function foo7();
                    final public function foo8() {}
                    abstract public function foo9();
                    public static function fooA() {}
                    public static function fooD() {}
                    final public static function fooE() {}
                    abstract public function fooF();
                        public function fooG ($foo) {}
                        public function fooH() {
                            static $foo;
                            $bar = function($baz) {};
                        }
                }
                EOD,
            <<<'EOD'
                <?php
                abstract class Foo {
                    public function& foo0() {}
                    public function & foo1() {}
                    function &foo2() {}
                    protected function foo3($b) {}
                    protected
                    abstract function foo4();
                    private function foo5() {}
                    final public function foo6() {}
                    abstract public function foo7();
                    public final function foo8() {}
                    public abstract function foo9();
                    public static function fooA() {}
                    public static
                    function fooD() {}
                    final static function fooE() {}
                    abstract function fooF();
                        function fooG ($foo) {}
                        function fooH() {
                            static $foo;
                            $bar = function($baz) {};
                        }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                abstract class Foo1 {
                    public function& foo0($a) {}
                }
                EOD,
            <<<'EOD'
                <?php
                abstract class Foo1 {
                    function& foo0($a) {}
                }
                EOD,
        ];

        yield 'leave functions alone' => [<<<'EOD'
            <?php
            function foo() {
                static $foo;
            }
            EOD];

        yield 'leave functions alone with variables matching OOP words' => [<<<'EOD'
            <?php
            function foo() {
                static $class;
                $interface = 'foo';
                $trait = 'bar';
            }
            EOD];

        yield 'leave functions alone inside conditionals' => [<<<'EOD'
            <?php
            if (!function_exists('foo')) {
                function foo($arg)
                {
                    return $arg;
                }
            }
            EOD];

        yield 'leave functions alone inside conditionals with OOP word in comment' => [<<<'EOD'
            <?php
            /* class <= this is just a stop-word */
            if (!function_exists('foo')) {
                function foo($arg)
                {
                    return $arg;
                }
            }
            EOD];

        yield 'leave functions alone with OOP word in comment' => [<<<'EOD'
            <?php
            /* class */
            function foo($arg)
            {
                return $arg;
            }
            EOD];

        yield 'leave functions alone outside classes with OOP word in inline HTML' => [<<<'EOD'
            <?php
            if (!function_exists('foo')) {
                function foo($arg)
                {
                ?>
                    <div class="test"></div>
                <?php
                    return $arg;
                }
            }
            EOD];

        yield 'leave functions alone outside classes with OOP word in string value' => [<<<'EOD'
            <?php
            if (!function_exists('foo')) {
                function foo($arg)
                {
                    return 'she has class right?';
                }
            }
            EOD];

        yield 'leave functions alone outside classes with OOP word in function name' => [<<<'EOD'
            <?php

            comment_class();

            if (!function_exists('foo')) {
                function foo($arg)
                {
                    return $arg;
                }
            }
            EOD];

        yield 'leave functions alone after class' => [<<<'EOD'
            <?php

            class Foo
            {
                public $foo;
            }

            if (!function_exists('bar')) {
                function bar()
                {
                    return 'bar';
                }
            }
            EOD];

        yield 'curly open syntax' => [<<<'EOD'
            <?php

            class Foo
            {
                private $bar;
                public function foo()
                {
                    $foo = "foo";
                    $fooA = "ab{$foo}cd";
                    $bar = "bar"; // test if variable after T_CURLY_OPEN is intact
                }
            }
            EOD];

        yield 'dollar open curly braces syntax' => [<<<'EOD'
            <?php

            class Foo {
                public function bar()
                {
                    $foo = "foo${width}foo";
                    $bar = "bar"; // test if variable after T_DOLLAR_OPEN_CURLY_BRACES is intact
                }
            }
            EOD];

        yield 'leave JavaScript outside PHP alone' => [<<<'EOD'
            <?php
            function foo()
            {
                return "foo";
            }
            ?>
            <script type="text/javascript">
            function foo(bar) {
                alert(bar);
            }
            </script>
            EOD];

        yield 'leave JavaScript in string alone' => [<<<'EOD'
            <?php
            function registerJS()
            {
            echo '<script type="text/javascript">
            function foo(bar) {
                alert(bar);
            }
            </script>';
            }
            EOD];

        yield 'leave JavaScript in variable alone' => [<<<'EOD'
            <?php
            class Foo
            {
                public function bar()
                {
                    $script = <<<JAVASCRIPT
            <script type="text/javascript">
            function foo(bar) {
                alert(bar);
            }
            </script>
            JAVASCRIPT;

                    return $script;
                }
            }
            EOD];

        yield 'comma separated properties' => [<<<'EOD'
            <?php
            class Foo
            {
                public $foo1;
                private $foo2;
                protected $bar1, $bar2;
                public $baz1 = null, $baz2, $baz3 = false;
                public $foo, $bar;
            }
            EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    var $foo1;
                    private $foo2;
                    protected $bar1, $bar2;
                    public $baz1 = null, $baz2, $baz3 = false;
                    var $foo, $bar;
                }
                EOD];

        yield 'var declarations with array value' => [<<<'EOD'
            <?php
            class Foo
            {
                public $foo1 = 1;
                public $foo2a = array('foo');
                public $foo2b = ['foo'];
                public $foo3a = array('foo', 'bar');
                public $foo3b = ['foo', 'bar'];
                public $foo4a = '1a', $foo5a = array(1, 2, 3), $foo6a = 10;
                public $foo4b = '1b', $foo5b = array(1, 2, 3), $foo6b = 10;
            }
            EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    var $foo1 = 1;
                    var $foo2a = array('foo');
                    var $foo2b = ['foo'];
                    var $foo3a = array('foo', 'bar');
                    var $foo3b = ['foo', 'bar'];
                    public $foo4a = '1a', $foo5a = array(1, 2, 3), $foo6a = 10;
                    public $foo4b = '1b', $foo5b = array(1, 2, 3), $foo6b = 10;
                }
                EOD];

        yield [
            '<?php class A { public const B=1; }',
            '<?php class A { const B=1; }',
            ['elements' => ['const']],
        ];

        yield [
            '<?php class A { public const B=1;public const C=1;/**/public const#a
                D=1;public const E=1;//
public const F=1; }',
            '<?php class A { const B=1;const C=1;/**/const#a
                D=1;const E=1;//
const F=1; }',
            ['elements' => ['const']],
        ];

        yield [
            '<?php class A { private const B=1; protected const C=2; public const D=4; public $a; function A(){} }',
            '<?php class A { private const B=1; protected const C=2; const D=4; public $a; function A(){} }',
            ['elements' => ['const']],
        ];

        yield [
            '<?php
                    class foo
                    {
                        public const A = 1, B =2, C =3;
                        public const TWO = ONE * 2;
                        public const THREE = ONE + self::TWO;
                        public const SENTENCE = "The value of THREE is ".self::THREE;
                    }
                ',
            '<?php
                    class foo
                    {
                        const A = 1, B =2, C =3;
                        const TWO = ONE * 2;
                        const THREE = ONE + self::TWO;
                        const SENTENCE = "The value of THREE is ".self::THREE;
                    }
                ',
            ['elements' => ['const']],
        ];

        yield 'comment' => [
            '<?php
class A
{# We will have a function below
# It will be static
# and awesome
public static function# <- this is the function
AB# <- this is the name
(#
)#
{#
}#
}
            ',
            '<?php
class A
{# We will have a function below
static# It will be static
# and awesome
function# <- this is the function
AB# <- this is the name
(#
)#
{#
}#
}
            ',
        ];

        yield 'anonymous class' => [
            '<?php
                $a = new class() {
                    public function a() {
                    }
                };

                class C
                {
                    public function A()
                    {
                        $a = new class() {public function a() {}};
                    }
                }
            ',
            '<?php
                $a = new class() {
                    function a() {
                    }
                };

                class C
                {
                    function A()
                    {
                        $a = new class() {function a() {}};
                    }
                }
            ',
        ];

        yield 'removing newlines between keywords' => [
            '<?php
                class Foo
                {
                    public $bar;

                    final public static function bar() {}

                    final public static function baz() {}
                }',
            '<?php
                class Foo
                {
                    var
                    $bar;

                    final
                    public
                    static
                    function bar() {}

                    static
                    final
                    function baz() {}
                }',
        ];

        yield 'keeping comment' => [
            '<?php
                class Foo
                {
                    /* constant */ private const BAR = 3;
                    /* variable */ private $bar;
                    /* function */ private function bar() {}
                }',
            '<?php
                class Foo
                {
                    private /* constant */ const BAR = 3;
                    private /* variable */ $bar;
                    private /* function */ function bar() {}
                }',
            ['elements' => ['property', 'method', 'const']],
        ];

        yield 'fixing with all keywords' => [
            '<?php
                abstract class Foo
                {
                    abstract protected static function fooA();
                    abstract protected static function fooB();
                    abstract protected static function fooC();
                    abstract protected static function fooD();
                    abstract protected static function fooE();
                    abstract protected static function fooF();
                    abstract public static function fooG();
                    abstract public static function fooH();
                }
            ',
            '<?php
                abstract class Foo
                {
                    abstract protected static function fooA();
                    abstract static protected function fooB();
                    protected abstract static function fooC();
                    protected static abstract function fooD();
                    static abstract protected function fooE();
                    static protected abstract function fooF();
                    abstract static function fooG();
                    static abstract function fooH();
                }
            ',
        ];

        yield [
            '<?php class Foo { private int $foo; }',
        ];

        yield [
            '<?php class Foo { protected ?string $foo; }',
        ];

        yield [
            '<?php class Foo { public ? string $foo; }',
        ];

        yield [
            '<?php class Foo { public ? string $foo; }',
            '<?php class Foo { var ? string $foo; }',
        ];

        yield [
            '<?php class Foo { public static Foo\Bar $foo; }',
            '<?php class Foo { static public Foo\Bar $foo; }',
        ];

        yield [
            '<?php class Foo { public array $foo; }',
        ];

        yield [
            '<?php class Foo { public ?array $foo; }',
        ];

        yield [
            '<?php class Foo { public static ?array $foo; }',
            '<?php class Foo { static public ?array $foo; }',
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
            '<?php class Foo { private int|float|null $foo; }',
        ];

        yield [
            '<?php class Foo { private int | /* or empty */ null $foo; }',
        ];

        yield [
            '<?php class Foo { private array|null $foo; }',
        ];

        yield [
            '<?php class Foo { private null|array $foo; }',
        ];

        yield [
            '<?php class Foo { public static null|array $foo; }',
            '<?php class Foo { static null|array $foo; }',
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
            '<?php class Foo { public Foo1&Bar $foo; }',
        ];

        yield [
            '<?php
class Foo
{
    public readonly string $prop2a;
}
            ',
            '<?php
class Foo
{
    readonly public string $prop2a;
}
            ',
        ];

        yield [
            '<?php
class Foo
{
    public readonly string $prop1;
    public readonly string $prop2;
}
            ',
            '<?php
class Foo
{
    readonly string $prop1;
    public readonly string $prop2;
}
            ',
        ];

        yield [
            '<?php
class Foo
{
    final public const B = "2";
}
',
            '<?php
class Foo
{
    public final const B = "2";
}
',
        ];

        yield [
            '<?php
class Foo
{
    final public const B = "2";
}
',
            '<?php
class Foo
{
    final const B = "2";
}
',
        ];

        yield [
            '<?php
enum Foo {
    case CAT;
    public function test(): self { return $this; }
}

var_dump(Foo::CAT->test());',
            '<?php
enum Foo {
    case CAT;
    function test(): self { return $this; }
}

var_dump(Foo::CAT->test());',
        ];
    }

    /**
     * @requires PHP 8.2
     *
     * @dataProvider provideFix82Cases
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield [
            '<?php trait Foo { public const Bar = 1; }',
            '<?php trait Foo { const Bar = 1; }',
        ];

        yield [
            '<?php class Foo {
                public (A&B)|C|D $x;
                protected A|(B&C)|D $y;
                private A|B|(C&D) $z;
            }',
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfiguration(array $config, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        $this->fixer->configure($config);
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string}>
     */
    public static function provideInvalidConfigurationCases(): iterable
    {
        yield 'invalid type' => [
            ['elements' => [null]],
            '/^\[visibility_required\] Invalid configuration: The option "elements" .*\.$/',
        ];

        yield 'invalid value' => [
            ['elements' => ['_unknown_']],
            '/^\[visibility_required\] Invalid configuration: The option "elements" .*\.$/',
        ];
    }
}
