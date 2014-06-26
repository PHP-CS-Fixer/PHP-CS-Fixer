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
    public function foo() {}
    public function foo() {}
    protected function foo() {}
    abstract protected function foo() {};
    private function foo() {}
    final public function foo() {}
    abstract public function foo();
    final public function foo() {}
    abstract public function foo();
    public static function foo() {}
    public static function foo() {}
    public static function foo() {}
    public static function foo() {}
    final public static function foo() {}
    abstract public function foo();
        function fooG ($foo) {}
        function fooH() {
    static $foo;
            $bar = function($baz) {};
        }
}
EOF;

        $input = <<<EOF
<?php
abstract class Foo {
    public function foo() {}
    function foo() {}
    protected function foo() {}
    protected
    abstract function foo() {};
    private function foo() {}
    final public function foo() {}
    abstract public function foo();
    public final function foo() {}
    public abstract function foo();
    public static function foo() {}
    public static function\tfoo() {}
    public static function
    foo() {}
    public static
    function foo() {}
    final static function foo() {}
    abstract function foo();
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
}
