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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\NoPhp4ConstructorFixer>
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoPhp4ConstructorFixer
 *
 * @author Matteo Beccati <matteo@beccati.com>
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

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
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
            <<<'EOF'
                <?php

                class Foo
                {
                    public function __construct($bar)
                    {
                        var_dump(1);
                    }
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    public function Foo($bar)
                    {
                        var_dump(1);
                    }
                }
                EOF,
        ];

        yield 'simple class 2' => [
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
                EOF,
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
                EOF,
        ];

        yield 'namespace' => [<<<'EOF'
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
            EOF];

        yield 'namespace 2' => [<<<'EOF'
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
            EOF];

        yield 'namespace global' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'PHP 5 only' => [<<<'EOF'
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
            EOF];

        yield 'PHP 4 only' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'both the right way 1' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'both the right way 2' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'both the right way 3' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'both the other way around 1' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'PHP 4 parent' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'PHP 4 parent init' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'mixed parent' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'mixed parent 2' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'parent other' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'parent other 2' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'class with anonymous' => [
            <<<'EOF'
                <?php

                class Foo {
                    private $bar;

                    public function __construct()
                    {
                        $this->bar = function () {};
                    }
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo {
                    private $bar;

                    public function Foo()
                    {
                        $this->bar = function () {};
                    }
                }
                EOF,
        ];

        yield 'class with comments' => [
            <<<'EOF'
                <?php
                class  /* test */
                // another

                Foo {
                public function /* test */ __construct($param) {
                }
                }
                EOF,
            <<<'EOF'
                <?php
                class  /* test */
                // another

                Foo {
                public function /* test */ Foo($param) {
                }
                }
                EOF,
        ];

        yield 'alpha beta' => [<<<'EOF'
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
            EOF];

        yield 'alpha beta trick 1' => [<<<'EOF'
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
            EOF];

        yield 'alpha beta trick 2' => [<<<'EOF'
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
            EOF];

        yield 'alpha beta trick 3' => [<<<'EOF'
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
            EOF];

        yield 'alpha beta trick 4 with another class' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'abstract' => [<<<'EOF'
            <?php

            abstract class Foo
            {
                abstract function Foo();
            }
            EOF];

        yield 'abstract trick' => [<<<'EOF'
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
            EOF];

        yield 'parent multiple classes' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
        ];

        yield 'infinite recursion' => [
            <<<'EOF'
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
                EOF,
            <<<'EOF'
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
                EOF,
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

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            <<<'EOF'
                <?php

                class Foo
                {
                    public function __construct($bar,)
                    {
                        var_dump(1);
                    }
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    public function Foo($bar,)
                    {
                        var_dump(1);
                    }
                }
                EOF,
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
