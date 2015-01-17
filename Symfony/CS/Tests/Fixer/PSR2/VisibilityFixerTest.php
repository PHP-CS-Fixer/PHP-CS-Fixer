<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class VisibilityFixerTest extends AbstractFixerTestBase
{
    public function testFixProperties()
    {
        $expected = <<<'EOF'
<?php
class Foo {
    public $var;
    protected $var_foo;
    private $FooBar;
    public static $var;
    protected static $var_foo;
    private static $FooBar;
    public static $var;
    protected static $var_foo;
    private static $FooBar;
    private static $FooBar;
    public $old = 'foo';
}
EOF;

        $input = <<<'EOF'
<?php
class Foo {
    public $var;
    protected $var_foo;
    private $FooBar;
    static public $var;
    static protected $var_foo;
    static private $FooBar;
    public static $var;
    protected static $var_foo;
    private static $FooBar;
    private static
    $FooBar;
    var $old = 'foo';
}
EOF;

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected);
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
    abstract protected function foo4() {};
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
    abstract function foo4() {};
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

        $this->makeTest($expected, $input);
    }

    public function testLeaveFunctionsAlone()
    {
        $expected = <<<'EOF'
<?php
function foo() {
    static $foo;
}
EOF;

        $this->makeTest($expected);
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

        $this->makeTest($expected);
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
        $this->makeTest($expected);
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

        $this->makeTest($expected);
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
        $this->makeTest($expected);
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
        $this->makeTest($expected);
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
        $this->makeTest($expected);
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
        $this->makeTest($expected);
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
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

        $this->makeTest($expected);
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
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

        $this->makeTest($expected);
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
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

        $this->makeTest($expected);
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
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

        $this->makeTest($expected);
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
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

        $this->makeTest($expected);
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
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

        $this->makeTest($expected);
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
    public function testLeaveCommaSeparatedPropertyAlone()
    {
        $expected = <<<'EOF'
<?php
class Foo
{
    public $foo;
    private $foo2;
    protected $bar1, $bar2;
    public $baz1 = null, $baz2, $baz3 = false;
}
EOF;

        $input = <<<'EOF'
<?php
class Foo
{
    $foo;
    private $foo2;
    protected $bar1, $bar2;
    public $baz1 = null, $baz2, $baz3 = false;
}
EOF;

        $this->makeTest($expected, $input);
    }
}
