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
    public $old = 'foo';
}
EOF;

        $input = <<<'EOF'
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
class Foo {
    public function foo() {}
    public function foo() {}
    protected function foo() {}
    private function foo() {}
    final public function foo() {}
    abstract public function foo();
    final public function foo() {}
    abstract public function foo();
    public static function foo() {}
    final public static function foo() {}
    abstract public static function foo();
        function ($foo) {}
        function() {
            static $foo;
        }
}
EOF;

        $input = <<<'EOF'
class Foo {
    public function foo() {}
    function foo() {}
    protected function foo() {}
    private function foo() {}
    final public function foo() {}
    abstract public function foo();
    public final function foo() {}
    public abstract function foo();
    public static function foo() {}
    final static function foo() {}
    static abstract function foo();
        function ($foo) {}
        function() {
            static $foo;
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
function foo() {
    static $foo;
}
EOF;

        $input = <<<'EOF'
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
function foo() {
    static $class;
    $interface = 'foo';
    $trait = 'bar;
}
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $expected));
    }
}
