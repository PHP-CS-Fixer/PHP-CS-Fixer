<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\VisibilityFixer;

class VisibilityFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFixProperties()
    {
        $fixer = new VisibilityFixer();
        $file = new \SplFileInfo(__FILE__);

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

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function testFixMethods()
    {
        $fixer = new VisibilityFixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
<?php
abstract class Foo {
    public function foo1() {}
    public function foo2() {}
    protected function foo3() {}
    abstract protected function foo4() {};
    private function foo5() {}
    final public function foo6() {}
    abstract public function foo7();
    final public function foo8() {}
    abstract public function foo9();
    public static function fooA() {}
    public static function fooB() {}
    public static function fooC() {}
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
    public function foo1() {}
    function foo2() {}
    protected function foo3() {}
    protected
    abstract function foo4() {};
    private function foo5() {}
    final public function foo6() {}
    abstract public function foo7();
    public final function foo8() {}
    public abstract function foo9();
    public static function fooA() {}
    public static function	fooB() {}
    public static function
    fooC() {}
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

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function testLeaveFunctionsAlone()
    {
        $fixer = new VisibilityFixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
<?php
function foo() {
    static $foo;
}
EOF;

        $input = <<<'EOF'
<?php
function foo() {
    static $foo;
}
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function testLeaveFunctionsAloneWithVariablesMatchingOopWords()
    {
        $fixer = new VisibilityFixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
<?php
function foo() {
    static $class;
    $interface = 'foo';
    $trait = 'bar';
}
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }

    public function testLeaveFunctionsAloneInsideConditionals()
    {
        $fixer = new VisibilityFixer();
        $file  = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
<?php
if (!function_exists('foo')) {
    function foo($arg)
    {
        return $arg;
    }
}
EOF;
        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }

    public function testLeaveFunctionsAloneInsideConditionalsWithOopWordInComment()
    {
        $fixer = new VisibilityFixer();
        $file  = new \SplFileInfo(__FILE__);

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
        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }

    public function testLeaveFunctionsAloneWithOopWordInComment()
    {
        $fixer = new VisibilityFixer();
        $file  = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
<?php
/* class */
function foo($arg)
{
    return $arg;
}
EOF;
        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }

    public function testLeaveFunctionsAloneOutsideClassesWithOopWordInInlineHtml()
    {
        $fixer = new VisibilityFixer();
        $file  = new \SplFileInfo(__FILE__);

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
        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }

    public function testLeaveFunctionsAloneOutsideClassesWithOopWordInStringValue()
    {
        $fixer = new VisibilityFixer();
        $file  = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
<?php
if (!function_exists('foo')) {
    function foo($arg)
    {
        return 'she has class right?';
    }
}
EOF;
        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }

    public function testLeaveFunctionsAloneOutsideClassesWithOopWordInFunctionName()
    {
        $fixer = new VisibilityFixer();
        $file  = new \SplFileInfo(__FILE__);

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
        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
    public function testLeaveFunctionsAloneAfterClass()
    {
        $fixer = new VisibilityFixer();
        $file  = new \SplFileInfo(__FILE__);

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

        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }

    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     */
    public function testLeaveComplexParsedVariableSyntaxAlone()
    {
        $fixer = new VisibilityFixer();
        $file  = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
<?php

class Foo
{
    private $bar;
    public function foo()
    {
        $foo = "foo";
        $fooA = "ab{$foo}cd"; // test T_CURLY_OPEN
        $bar = "bar"; // test if nested curly braces are counting properly after T_CURLY_OPEN
    }
}
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }
}
