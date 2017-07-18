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
        $expected = <<<'EOF'
<?php
class Foo {
    public function aaa() {}
    public $bbb;
}
EOF;

        $this->doTest($expected);
    }

    public function testFixMethods()
    {
        $expected = <<<'EOF'
<?php
abstract class Foo {
    public function& foo0() {}
    public function & foo1() {}
    public function &foo2() {}
    protected function foo3() {}
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
EOF;

        $input = <<<'EOF'
<?php
abstract class Foo {
    public function& foo0() {}
    public function & foo1() {}
    function &foo2() {}
    protected function foo3() {}
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
EOF;

        $this->doTest($expected, $input);
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
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '/^\[visibility_required\] Invalid configuration: The option "elements" .*\.$/'
        );

        $this->fixer->configure(array('elements' => array(null)));
    }

    public function testInvalidConfigurationValue()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '/^\[visibility_required\] Invalid configuration: The option "elements" .*\.$/'
        );

        $this->fixer->configure(array('elements' => array('_unknown_')));
    }

    public function testInvalidConfigurationValueForPHPVersion()
    {
        if (PHP_VERSION_ID >= 70100) {
            $this->markTestSkipped('PHP version to high.');
        }

        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '/^\[visibility_required\] Invalid configuration for env: "const" option can only be enabled with PHP 7\.1\+\.$/'
        );

        $this->fixer->configure(array('elements' => array('const')));
    }

    /**
     * @param string $expected expected PHP source after fixing
     * @param string $input    PHP source to fix
     *
     * @group legacy
     * @requires PHP 7.1
     * @dataProvider provideClassConstTest
     * @expectedDeprecation Passing "elements" at the root of the configuration is deprecated and will not be supported in 3.0, use "elements" => array(...) option instead.
     */
    public function testLegacyFixClassConst($expected, $input)
    {
        $this->fixer->configure(array('const'));
        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected expected PHP source after fixing
     * @param string $input    PHP source to fix
     *
     * @requires PHP 7.1
     * @dataProvider provideClassConstTest
     */
    public function testFixClassConst($expected, $input)
    {
        $this->fixer->configure(array('elements' => array('const')));
        $this->doTest($expected, $input);
    }

    public function provideClassConstTest()
    {
        return array(
            array(
                '<?php class A { public const B=1; }',
                '<?php class A { const B=1; }',
            ),
            array(
                '<?php class A { public const B=1;public const C=1;/**/public const#a
                D=1;public const E=1;//
public const F=1; }',
                '<?php class A { const B=1;const C=1;/**/const#a
                D=1;const E=1;//
const F=1; }',
            ),
            array(
                '<?php class A { private const B=1; protected const C=2; public const D=4; public $a; function A(){} }',
                '<?php class A { private const B=1; protected const C=2; const D=4; public $a; function A(){} }',
            ),
            array(
                '<?php
                    class foo
                    {
                        public const A = 1, B =2, C =3;
                        // As of PHP 5.6.0
                        public const TWO = ONE * 2;
                        public const THREE = ONE + self::TWO;
                        public const SENTENCE = "The value of THREE is ".self::THREE;
                    }
                ',
                '<?php
                    class foo
                    {
                        const A = 1, B =2, C =3;
                        // As of PHP 5.6.0
                        const TWO = ONE * 2;
                        const THREE = ONE + self::TWO;
                        const SENTENCE = "The value of THREE is ".self::THREE;
                    }
                ',
            ),
        );
    }

    public function testCommentCases()
    {
        $expected = '<?php
class A
{#
public static function#
AB#
(#
)#
{#
}#
}
        ';

        $input = '<?php
class A
{#
static#
#
function#
AB#
(#
)#
{#
}#
}
        ';

        $this->doTest($expected, $input);
    }
}
