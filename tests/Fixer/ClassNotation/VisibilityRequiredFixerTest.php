<?php

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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer
 */
final class VisibilityRequiredFixerTest extends AbstractFixerTestCase
{
    public function testFixProperties()
    {
        $expected = <<<'EOF'
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
EOF;

        $input = <<<'EOF'
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
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixPropertiesAfterMethod()
    {
        $input = <<<'EOF'
<?php
class Foo {
    public function aaa() {}
    var $bbb;
}
EOF;
        $expected = <<<'EOF'
<?php
class Foo {
    public function aaa() {}
    public $bbb;
}
EOF;

        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFixMethodsCases
     */
    public function testFixMethods($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @requires PHP 7.0
     * @dataProvider provideFixMethods70Cases
     */
    public function testFixMethods70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixMethods70Cases()
    {
        return [
            [
                <<<'EOF'
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
EOF
                ,
                <<<'EOF'
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
EOF
                ,
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideFixMethodsCases()
    {
        return [
            [
                <<<'EOF'
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
EOF
                ,
                <<<'EOF'
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
EOF
                ,
            ],
            [
                <<<'EOF'
<?php
abstract class Foo1 {
    public function& foo0($a) {}
}
EOF
                ,
                <<<'EOF'
<?php
abstract class Foo1 {
    function& foo0($a) {}
}
EOF
                ,
            ],
        ];
    }

    public function testLeaveFunctionsAlone()
    {
        $expected = <<<'EOF'
<?php
function foo() {
    static $foo;
}
EOF;

        $this->doTest($expected);
    }

    public function testLeaveFunctionsAloneWithVariablesMatchingOopWords()
    {
        $expected = <<<'EOF'
<?php
function foo() {
    static $class;
    $interface = 'foo';
    $trait = 'bar';
}
EOF;

        $this->doTest($expected);
    }

    public function testLeaveFunctionsAloneInsideConditionals()
    {
        $expected = <<<'EOF'
<?php
if (!function_exists('foo')) {
    function foo($arg)
    {
        return $arg;
    }
}
EOF;
        $this->doTest($expected);
    }

    public function testLeaveFunctionsAloneInsideConditionalsWithOopWordInComment()
    {
        $expected = <<<'EOF'
<?php
/* class <= this is just a stop-word */
if (!function_exists('foo')) {
    function foo($arg)
    {
        return $arg;
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testLeaveFunctionsAloneWithOopWordInComment()
    {
        $expected = <<<'EOF'
<?php
/* class */
function foo($arg)
{
    return $arg;
}
EOF;
        $this->doTest($expected);
    }

    public function testLeaveFunctionsAloneOutsideClassesWithOopWordInInlineHtml()
    {
        $expected = <<<'EOF'
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
EOF;
        $this->doTest($expected);
    }

    public function testLeaveFunctionsAloneOutsideClassesWithOopWordInStringValue()
    {
        $expected = <<<'EOF'
<?php
if (!function_exists('foo')) {
    function foo($arg)
    {
        return 'she has class right?';
    }
}
EOF;
        $this->doTest($expected);
    }

    public function testLeaveFunctionsAloneOutsideClassesWithOopWordInFunctionName()
    {
        $expected = <<<'EOF'
<?php

comment_class();

if (!function_exists('foo')) {
    function foo($arg)
    {
        return $arg;
    }
}
EOF;
        $this->doTest($expected);
    }

    public function testLeaveFunctionsAloneAfterClass()
    {
        $expected = <<<'EOF'
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
EOF;

        $this->doTest($expected);
    }

    public function testCurlyOpenSyntax()
    {
        $expected = <<<'EOF'
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
EOF;

        $this->doTest($expected);
    }

    public function testDollarOpenCurlyBracesSyntax()
    {
        $expected = <<<'EOF'
<?php

class Foo {
    public function bar()
    {
        $foo = "foo${width}foo";
        $bar = "bar"; // test if variable after T_DOLLAR_OPEN_CURLY_BRACES is intact
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testLeaveJavascriptOutsidePhpAlone()
    {
        $expected = <<<'EOF'
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
EOF;

        $this->doTest($expected);
    }

    public function testLeaveJavascriptInStringAlone()
    {
        $expected = <<<'EOF'
<?php
function registerJS()
{
echo '<script type="text/javascript">
function foo(bar) {
    alert(bar);
}
</script>';
}
EOF;

        $this->doTest($expected);
    }

    public function testLeaveJavascriptInVariableAlone()
    {
        $expected = <<<'EOF'
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
EOF;

        $this->doTest($expected);
    }

    public function testFixCommaSeparatedProperty()
    {
        $expected = <<<'EOF'
<?php
class Foo
{
    public $foo1;
    private $foo2;
    protected $bar1, $bar2;
    public $baz1 = null, $baz2, $baz3 = false;
    public $foo, $bar;
}
EOF;

        $input = <<<'EOF'
<?php
class Foo
{
    var $foo1;
    private $foo2;
    protected $bar1, $bar2;
    public $baz1 = null, $baz2, $baz3 = false;
    var $foo, $bar;
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixesVarDeclarationsWithArrayValue()
    {
        $expected = <<<'EOF'
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
EOF;

        $input = <<<'EOF'
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
EOF;

        $this->doTest($expected, $input);
    }

    public function testInvalidConfigurationType()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[visibility_required\] Invalid configuration: The option "elements" .*\.$/');

        $this->fixer->configure(['elements' => [null]]);
    }

    public function testInvalidConfigurationValue()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[visibility_required\] Invalid configuration: The option "elements" .*\.$/');

        $this->fixer->configure(['elements' => ['_unknown_']]);
    }

    /**
     * @requires PHP <7.1
     */
    public function testInvalidConfigurationValueForPHPVersion()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[visibility_required\] Invalid configuration for env: "const" option can only be enabled with PHP 7\.1\+\.$/');

        $this->fixer->configure(['elements' => ['const']]);
    }

    /**
     * @param string $expected expected PHP source after fixing
     * @param string $input    PHP source to fix
     *
     * @requires PHP 7.1
     * @dataProvider provideFixClassConstCases
     */
    public function testFixClassConst($expected, $input)
    {
        $this->fixer->configure(['elements' => ['const']]);
        $this->doTest($expected, $input);
    }

    public function provideFixClassConstCases()
    {
        return [
            [
                '<?php class A { public const B=1; }',
                '<?php class A { const B=1; }',
            ],
            [
                '<?php class A { public const B=1;public const C=1;/**/public const#a
                D=1;public const E=1;//
public const F=1; }',
                '<?php class A { const B=1;const C=1;/**/const#a
                D=1;const E=1;//
const F=1; }',
            ],
            [
                '<?php class A { private const B=1; protected const C=2; public const D=4; public $a; function A(){} }',
                '<?php class A { private const B=1; protected const C=2; const D=4; public $a; function A(){} }',
            ],
            [
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
            ],
        ];
    }

    public function testCommentCases()
    {
        $expected = '<?php
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
        ';

        $input = '<?php
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
        ';

        $this->doTest($expected, $input);
    }

    /**
     * @requires PHP 7.0
     */
    public function testAnonymousClassFixing()
    {
        $this->doTest(
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
            '
        );
    }

    public function testRemovingNewlinesBetweenKeywords()
    {
        $this->doTest(
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
                }'
        );
    }

    /**
     * @requires PHP 7.1
     */
    public function testKeepingComment()
    {
        $this->fixer->configure(['elements' => ['property', 'method', 'const']]);

        $this->doTest(
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
                }'
        );
    }

    public function testFixingWithAllKeywords()
    {
        $this->doTest(
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
            '
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.4
     * @dataProvider provideFix74Cases
     */
    public function testFix74($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix74Cases()
    {
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
}
