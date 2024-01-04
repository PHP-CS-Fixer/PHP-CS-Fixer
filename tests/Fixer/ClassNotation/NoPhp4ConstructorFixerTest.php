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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $a = new class {};',
        ];

        yield [
            '<?php $a = new class {}?>',
        ];

        yield [
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
        ];

        yield 'simple class 1' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    public function __construct($bar)
                    {
                        var_dump(1);
                    }
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    public function Foo($bar)
                    {
                        var_dump(1);
                    }
                }
                EOD
        ];

        yield 'simple class 2' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'namespace' => [<<<'EOD'
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
            EOD];

        yield 'namespace 2' => [<<<'EOD'
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
            EOD];

        yield 'namespace global' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'PHP 5 only' => [<<<'EOD'
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
            EOD];

        yield 'PHP 4 only' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'both the right way 1' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'both the right way 2' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'both the right way 3' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'both the other way around 1' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'PHP 4 parent' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'PHP 4 parent init' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'mixed parent' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'mixed parent 2' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'parent other' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'parent other 2' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'class with anonymous' => [
            <<<'EOD'
                <?php

                class Foo {
                    private $bar;

                    public function __construct()
                    {
                        $this->bar = function () {};
                    }
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo {
                    private $bar;

                    public function Foo()
                    {
                        $this->bar = function () {};
                    }
                }
                EOD
        ];

        yield 'class with comments' => [
            <<<'EOD'
                <?php
                class  /* test */
                // another

                Foo {
                public function /* test */ __construct($param) {
                }
                }
                EOD,
            <<<'EOD'
                <?php
                class  /* test */
                // another

                Foo {
                public function /* test */ Foo($param) {
                }
                }
                EOD
        ];

        yield 'alpha beta' => [<<<'EOD'
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
            EOD];

        yield 'alpha beta trick 1' => [<<<'EOD'
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
            EOD];

        yield 'alpha beta trick 2' => [<<<'EOD'
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
            EOD];

        yield 'alpha beta trick 3' => [<<<'EOD'
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
            EOD];

        yield 'alpha beta trick 4 with another class' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'abstract' => [<<<'EOD'
            <?php

            abstract class Foo
            {
                abstract function Foo();
            }
            EOD];

        yield 'abstract trick' => [<<<'EOD'
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
            EOD];

        yield 'parent multiple classes' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'infinite recursion' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php

                class Foo
                {
                    public function __construct($bar,)
                    {
                        var_dump(1);
                    }
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    public function Foo($bar,)
                    {
                        var_dump(1);
                    }
                }
                EOD
        ];

        yield [
            '<?php
class Foo
{
    public function __construct()
    {
    }
}',
            '<?php
class Foo
{
    public function Foo()
    {
    }
}',
        ];

        yield [
            '<?php
class Foo
{
    public function __construct()
    {
        $this?->__construct();
    }
}',
            '<?php
class Foo
{
    public function Foo()
    {
        $this?->__construct();
    }
}',
        ];

        yield [
            '<?php
class Foo extends Bar
{
    public function __construct()
    {
        parent::__construct();
    }
}',
            '<?php
class Foo extends Bar
{
    public function Foo()
    {
        $this?->Bar();
    }
}',
        ];

        yield [
            '<?php
class Foo
{
    /**
     * Constructor
     */
    public function __construct($bar = 1, $baz = null)
    {
        var_dump(1);
    }
}
',
            '<?php
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
        $this?->__construct($bar, $baz);
    }
}
',
        ];
    }
}
