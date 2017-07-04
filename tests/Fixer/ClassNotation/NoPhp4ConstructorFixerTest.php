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
 * @author Matteo Beccati <matteo@beccati.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoPhp4ConstructorFixer
 */
final class NoPhp4ConstructorFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provide70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provide70Cases()
    {
        return [
            [
                '<?php $a = new class {};',
            ],
            [
                '<?php $a = new class {}?>',
            ],
            [
                '<?php
                    $a = new Foo() <=> 1;
                    $a = new Foo <=> 1;
                    $a = new class() {};
                    $a = new class() implements Foo{};
                    $a = new class() /**/ extends Bar1{};
                    $a = new class()  extends Bar2 implements Foo{};
                    $a = new class()    extends Bar3 implements Foo, Foo2{};
                    $a = new class() {};
                    $a = new class {};
                    $a = new class implements Foo{};
                    $a = new class /**/ extends Bar1{};
                    $a = new class  extends Bar2 implements Foo{};
                    $a = new class    extends Bar3 implements Foo, Foo2{};
                    $a = new class {}?>
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideSimpleCases
     */
    public function testSimpleClass($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideSimpleCases()
    {
        return [
            [
            <<<'EOF'
<?php

class Foo
{
    public function __construct($bar)
    {
        var_dump(1);
    }
}
EOF
        ,
            <<<'EOF'
<?php

class Foo
{
    public function Foo($bar)
    {
        var_dump(1);
    }
}
EOF
            ],
            [
                <<<'EOF'
<?php

class Foo
{
    public#
    function#
__construct#
    (#
    $bar#
    )#
    {}
}
EOF
        ,
            <<<'EOF'
<?php

class Foo
{
    public#
    function#
Foo#
    (#
    $bar#
    )#
    {}
}
EOF
            ],
        ];
    }

    public function testNamespaces()
    {
        $expected = <<<'EOF'
<?php

namespace Baz\Qux;

class Foo
{
    public function __construct($bar)
    {
        var_dump(1);
    }

    public function Foo($bar)
    {
        var_dump(2);
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testNamespaces2()
    {
        $expected = <<<'EOF'
<?php

namespace Baz\Qux
{
    class Foo
    {
        public function __construct($bar)
        {
            var_dump(1);
        }

        public function Foo($bar)
        {
            var_dump(2);
        }
    }

    class Bar
    {
        public function Bar()
        {
            var_dump(3);
        }
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testNamespaceGlobal()
    {
        $expected = <<<'EOF'
<?php

namespace {
    class Foo
    {
        function __construct($bar)
        {
            var_dump(1);
        }
    }
}
EOF;

        $input = <<<'EOF'
<?php

namespace {
    class Foo
    {
        function Foo($bar)
        {
            var_dump(1);
        }
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testPhp5Only()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    function __construct($bar)
    {
        var_dump(1);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testPhp4Only()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    /**
     * Constructor
     */
    function __construct($bar)
    {
        var_dump(1);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo
{
    /**
     * Constructor
     */
    function foO($bar)
    {
        var_dump(1);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testBothTheRightWay1()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    /**
     * Constructor
     */
    public function __construct()
    {
        var_dump(1);
    }

    public function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo
{
    /**
     * Constructor
     */
    public function __construct()
    {
        var_dump(1);
    }

    /**
     * PHP-4 Constructor
     */
    function Foo()
    {
        // Call PHP5!
        $this->__construct();
    }

    public function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testBothTheRightWay2()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    /**
     * Constructor
     */
    public function __construct($bar)
    {
        var_dump(1);
    }

    public function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo
{
    /**
     * Constructor
     */
    public function __construct($bar)
    {
        var_dump(1);
    }

    /**
     * PHP-4 Constructor
     */
    function Foo($bar)
    {
        // Call PHP5!
        $this->__construct($bar);
    }

    public function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testBothTheRightWay3()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    /**
     * Constructor
     */
    public function __construct($bar = 1, $baz = null)
    {
        var_dump(1);
    }

    public function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo
{
    /**
     * Constructor
     */
    public function __construct($bar = 1, $baz = null)
    {
        var_dump(1);
    }

    /**
     * PHP-4 Constructor
     */
    function Foo($bar = 1, $baz = null)
    {
        // Call PHP5!
        $this->__construct($bar, $baz);
    }

    public function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testBothTheOtherWayAround()
    {
        $expected = <<<'EOF'
<?php

class Foo
{

    /**
     * PHP-4 Constructor.
     *
     * This is the real constructor. It's the one that most likely contains any meaningful info in the docblock.
     */
    private function __construct($bar)
    {
        var_dump(1);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo
{
    /**
     * PHP-5 Constructor.
     *
     * This docblock is removed, along with the entire wrapper method.
     */
    protected function __construct($bar)
    {
        // Call The Real Constructor, not the hippy fake one!
        $this->Foo($bar);
    }

    /**
     * PHP-4 Constructor.
     *
     * This is the real constructor. It's the one that most likely contains any meaningful info in the docblock.
     */
    private function Foo($bar)
    {
        var_dump(1);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testPhp4Parent()
    {
        $expected = <<<'EOF'
<?php

class Foo extends FooParEnt
{
    /**
     * Constructor
     */
    function __construct($bar)
    {
        parent::__construct(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo extends FooParEnt
{
    /**
     * Constructor
     */
    function Foo($bar)
    {
        parent::FooPaRent(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testPhp4ParentInit()
    {
        $expected = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function __construct($bar)
    {
        parent::init(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function Foo($bar)
    {
        parent::init(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testMixedParent()
    {
        $expected = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function __construcT($bar)
    {
        parent::__construct(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function __construcT($bar)
    {
        parent::FooParenT(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testMixedParent2()
    {
        $expected = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function __construcT($bar)
    {
        parent::__construct(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function __construcT($bar)
    {
        $this->FooParenT(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testParentOther()
    {
        $expected = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function __construct($bar)
    {
        parent::__construct(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function Foo($bar)
    {
        $this->FooParent(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testParentOther2()
    {
        $expected = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function __construct($bar)
    {
        parent::__construct(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo extends FooParent
{
    /**
     * Constructor
     */
    function Foo($bar)
    {
        FooParent::FooParent(1);
        var_dump(9);
    }

    function bar()
    {
        var_dump(3);
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testClassWithAnonymous()
    {
        $expected = <<<'EOF'
<?php

class Foo {
    private $bar;

    public function __construct()
    {
        $this->bar = function () {};
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo {
    private $bar;

    public function Foo()
    {
        $this->bar = function () {};
    }
}
EOF;
        $this->doTest($expected, $input);
    }

    public function testClassWithComments()
    {
        $expected = <<<'EOF'
<?php
class  /* test */
// another

Foo {
public function /* test */ __construct($param) {
}
}
EOF;

        $input = <<<'EOF'
<?php
class  /* test */
// another

Foo {
public function /* test */ Foo($param) {
}
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testAlphaBeta()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    public function Foo()
    {
        echo 'alpha';
    }
    public function __construct()
    {
        echo 'beta';
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testAlphaBetaTrick1()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    public function Foo()
    {
        // This is not $this->__construct()
        echo 'alpha';
    }
    public function __construct()
    {
        echo 'beta';
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testAlphaBetaTrick2()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    public function Foo()
    {
        echo 'alpha';
    }
    public function __construct()
    {
        // This is not $this->Foo()
        echo 'beta';
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testAlphaBetaTrick3()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    public function Foo()
    {
        echo 'alpha';
        /* yeah, ok let's construct it anyway */
        $this->__construct();
    }
    public function __construct()
    {
        echo 'beta';
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testAlphaBetaTrick4WithAnotherClass()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    public function Foo()
    {
        echo 'alpha';
    }
    public function __construct()
    {
        $this->Foo();
        // Do something more!
        echo 'beta';
    }
}

Class Bar
{
    function __construct()
    {
        $this->foo = 1;
    }
}
EOF;

        $input = <<<'EOF'
<?php

class Foo
{
    public function Foo()
    {
        echo 'alpha';
    }
    public function __construct()
    {
        $this->Foo();
        // Do something more!
        echo 'beta';
    }
}

Class Bar
{
    function bar()
    {
        $this->foo = 1;
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testAbstract()
    {
        $expected = <<<'EOF'
<?php

abstract class Foo
{
    abstract function Foo();
}
EOF;

        $this->doTest($expected);
    }

    public function testAbstractTrick()
    {
        $expected = <<<'EOF'
<?php

abstract class Foo
{
    abstract public function Foo();

    public function bar()
    {
        // This is messed up, I know
        $this->__construct();
    }

    public function __construct()
    {
        $this->baz = 1;
    }
}
EOF;

        $this->doTest($expected);
    }

    public function testParentMultipleClasses()
    {
        $expected = <<<'EOF'
<?php
    class Class1 extends Parent1
    {
        function __construct($foo)
        {
            parent::__construct();
            echo "something";
        }
    }

    class Class2 extends Parent2
    {
        function __construct($foo)
        {
            echo "something";
        }
    }
?>
EOF;

        $input = <<<'EOF'
<?php
    class Class1 extends Parent1
    {
        function __construct($foo)
        {
            $this->Parent1();
            echo "something";
        }
    }

    class Class2 extends Parent2
    {
        function __construct($foo)
        {
            echo "something";
        }
    }
?>
EOF;

        $this->doTest($expected, $input);
    }

    public function testInfiniteRecursion()
    {
        $expected = <<<'EOF'
<?php
    class Parent1
    {
        function __construct()
        {
            echo "foobar";
        }
    }

    class Class1 extends Parent1
    {
        function __construct($foo)
        {
            parent::__construct();
            echo "something";
        }
    }
?>
EOF;

        $input = <<<'EOF'
<?php
    class Parent1
    {
        function __construct()
        {
            echo "foobar";
        }
    }

    class Class1 extends Parent1
    {
        function Class1($foo)
        {
            $this->__construct();
            echo "something";
        }
    }
?>
EOF;

        $this->doTest($expected, $input);
    }
}
