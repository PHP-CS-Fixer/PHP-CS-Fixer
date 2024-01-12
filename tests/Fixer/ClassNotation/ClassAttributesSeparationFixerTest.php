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
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer
 */
final class ClassAttributesSeparationFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, bool> $configuration
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
        yield [
            <<<'EOD'
                <?php
                class Sample
                {
                private $a; // foo

                    /** second in a hour */
                    private $b;
                }

                EOD,
            <<<'EOD'
                <?php
                class Sample
                {private $a; // foo
                    /** second in a hour */
                    private $b;
                }

                EOD,
        ];

        yield 'empty class' => [
            '<?php class Foo {}',
        ];

        yield 'simple top class' => [
            <<<'EOD'
                <?php class A {
                public function Foo(){}
                }
                EOD,
            '<?php class A {public function Foo(){}}',
        ];

        yield 'comment' => [
            <<<'EOD'
                <?php class A {
                /* function comment */
                public function Bar(){}
                }
                EOD,
            <<<'EOD'
                <?php class A {/* function comment */public function Bar(){}
                }
                EOD,
        ];

        yield 'comment, multiple lines' => [
            <<<'EOD'
                <?php class A {
                /* some comment */

                public function Bar(){}
                }
                EOD,
            <<<'EOD'
                <?php class A {
                /* some comment */



                public function Bar(){}
                }
                EOD,
        ];

        yield 'simple PHPDoc case' => [
            <<<'EOD'
                <?php class Foo
                {
                /** Doc 1 */
                public function A(){}

                    /** Doc 2 */
                    public function B(){}
                }
                EOD,
            <<<'EOD'
                <?php class Foo
                {/** Doc 1 */public function A(){}

                    /** Doc 2 */

                    public function B(){}
                }
                EOD,
        ];

        yield 'add a newline at the end of a class with trait group' => [
            <<<'EOD'
                <?php class A
                {
                    use Bar {
                        __construct as barConstruct;
                        baz as barBaz;
                    }
                }
                EOD,
            <<<'EOD'
                <?php class A
                {
                    use Bar {
                        __construct as barConstruct;
                        baz as barBaz;
                    }}
                EOD,
        ];

        yield 'add a newline at the end of a class with trait' => [
            <<<'EOD'
                <?php class A
                {
                    use A\B\C;
                }
                EOD,
            <<<'EOD'
                <?php class A
                {
                    use A\B\C;}
                EOD,
        ];

        yield 'removes extra lines at the end of an interface' => [
            <<<'EOD'
                <?php interface F
                {
                    public function A();
                }
                EOD,
            <<<'EOD'
                <?php interface F
                {
                    public function A();


                }
                EOD,
        ];

        yield 'removes extra lines at the end of an abstract class' => [
            <<<'EOD'
                <?php abstract class F
                {
                    public abstract function A();
                }
                EOD,
            <<<'EOD'
                <?php abstract class F
                {
                    public abstract function A();


                }
                EOD,
        ];

        yield 'add a newline at the end of a class' => [
            <<<'EOD'
                <?php class A
                {
                    public function A(){}
                }
                EOD,
            <<<'EOD'
                <?php class A
                {
                    public function A(){}}
                EOD,
        ];

        yield 'add a newline at the end of a class: with comments' => [
            <<<'EOD'
                <?php class A
                {
                    public const A = 1; /* foo */ /* bar */
                }
                EOD,
            <<<'EOD'
                <?php class A
                {
                    public const A = 1; /* foo */ /* bar */}
                EOD,
        ];

        yield 'add a newline at the end of a class: with comments with trailing space' => [
            <<<'EOD'
                <?php class A
                {
                    public const A = 1; /* foo */ /* bar */
                   }
                EOD,
            <<<'EOD'
                <?php class A
                {
                    public const A = 1; /* foo */ /* bar */   }
                EOD,
        ];

        $to = $from = '<?php ';

        for ($i = 0; $i < 15; ++$i) {
            $from .= sprintf('class A%d{public function GA%d(){return new class {public function B6B%d(){}};}public function otherFunction%d(){}}', $i, $i, $i, $i);
            $to .= sprintf("class A%d{\npublic function GA%d(){return new class {\npublic function B6B%d(){}\n};}\n\npublic function otherFunction%d(){}\n}", $i, $i, $i, $i);
        }

        yield [$to, $from];

        yield [
            <<<'EOD'
                <?php $a = new class {
                                public function H(){}

                                public function B7(){}

                                private function C(){}
                                };
                EOD,
            <<<'EOD'
                <?php $a = new class {
                                public function H(){}
                                public function B7(){}
                                private function C(){}
                                };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                public function getFilter()
                                        {
                                            return new class () implements FilterInterface {
                private $d = 123;

                                                public function pass($a, $b) {
                                                    echo $a;
                                                }

                                                public $e = 5;
                };}
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {public function getFilter()
                                        {
                                            return new class () implements FilterInterface {private $d = 123;
                                                public function pass($a, $b) {
                                                    echo $a;
                                                }
                                                public $e = 5;};}



                                    }
                EOD."\n                ",
        ];

        yield [<<<'EOD'
            <?php
            class SomeClass1
            {
                // This comment
                // is multiline.
                public function echoA()
                {
                    echo "a";
                }
            }

            EOD];

        yield [
            <<<'EOD'
                <?php
                class SomeClass2
                {
                    // This comment
                    /* is multiline. */
                public function echoA()
                    {
                        echo "a";
                    }
                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                class SomeClass2
                {
                    // This comment
                    /* is multiline. */public function echoA()
                    {
                        echo "a";
                    }
                }
                EOD."\n            ",
        ];

        yield [
            <<<'EOD'
                <?php
                class SomeClass3
                {
                    // This comment
                    // is multiline.

                    public function echoA()
                    {
                        echo "a";
                    }
                }

                EOD, ];

        yield [
            <<<'EOD'
                <?php
                class SomeClass1
                {
                    private $a; //

                    public function methodA()
                    {
                    }

                    private $b;

                    //
                    public function methodB()
                    {
                    }

                    // C
                    public function methodC()
                    {
                    }

                    // D

                    public function methodD()
                    {
                    }

                    /* E */

                    public function methodE()
                    {
                    }

                    /* F */
                    public function methodF()
                    {
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                class SomeClass1
                {
                    private $a; //
                    public function methodA()
                    {
                    }

                    private $b;
                    //
                    public function methodB()
                    {
                    }
                    // C
                    public function methodC()
                    {
                    }

                    // D

                    public function methodD()
                    {
                    }

                    /* E */

                    public function methodE()
                    {
                    }

                    /* F */
                    public function methodF()
                    {
                    }
                }

                EOD, ];

        yield [<<<'EOD'
            <?php
            class SomeClass
            {
                // comment
                public function echoA()
                {
                    echo "a";
                }
            }

            EOD];

        yield [<<<'EOD'
            <?php
            class SomeClass
            {
                // This comment
                // is multiline.
                public function echoA()
                {
                    echo "a";
                }
            }

            EOD];

        yield [
            <<<'EOD'
                <?php
                class SomeClass
                {
                    // comment

                    public function echoA()
                    {
                        echo "a";
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                class SomeClass
                {
                    // comment


                    public function echoA()
                    {
                        echo "a";
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class SomeClass
                {
                    /* comment */
                public function echoB()
                    {
                        echo "a";
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                class SomeClass
                {
                    /* comment */public function echoB()
                    {
                        echo "a";
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class SomeClass
                {
                    /* comment */
                 public function echoC()
                    {
                        echo "a";
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                class SomeClass
                {
                    /* comment */ public function echoC()
                    {
                        echo "a";
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                abstract class MethodTest2
                {
                    public function method045()
                    {
                        $files = null;
                            if (!empty($files)) {
                            $this->filter(
                                function (\SplFileInfo $file) use ($files) {
                                    return !in_array($file->getRelativePathname(), $files, true);
                                }
                            );
                        }
                    }

                     private $a;

                     public static function method145()
                     {
                     }

                       abstract protected function method245();
                    // comment

                    final private function method345()
                    {
                    }
                }
                function some1(){ echo 1;}
                function some2(){ echo 2;}
                EOD,
            <<<'EOD'
                <?php
                abstract class MethodTest2
                {
                    public function method045()
                    {
                        $files = null;
                            if (!empty($files)) {
                            $this->filter(
                                function (\SplFileInfo $file) use ($files) {
                                    return !in_array($file->getRelativePathname(), $files, true);
                                }
                            );
                        }
                    }
                     private $a;

                     public static function method145()
                     {
                     }
                       abstract protected function method245();
                    // comment

                    final private function method345()
                    {
                    }
                }
                function some1(){ echo 1;}
                function some2(){ echo 2;}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                /*
                 * This file is part of the PHP CS utility.
                 *
                 * (c) Fabien Potencier <fabien@symfony.com>
                 *
                 * This source file is subject to the MIT license that is bundled
                 * with this source code in the file LICENSE.
                 */

                namespace PhpCsFixer\Linter;

                /**
                 * Dummy linter. No linting is performed. No error is raised.
                 *
                 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
                 *
                 * @internal
                 */
                final class NullLinter implements LinterInterface
                {
                    /**
                     * {@inheritdoc}
                     */
                    public function lintFile($path)
                    {
                        unset($path);
                    }

                    /**
                     * {@inheritdoc}
                     */
                    public function lintSource($source)
                    {
                        unset($source);
                    }
                }

                EOD,
        ];

        // do not touch anonymous functions (since PHP doesn't allow
        // for class attributes being functions :(, we only have to test
        // those used within methods)
        yield [
            <<<'EOD'
                <?php
                class MethodTestAnonymous
                {
                    public function method444a()
                    {
                        $text = "hello";
                        $example = function ($arg) use ($message) {
                            var_dump($arg . " " . $message);
                        };
                        $example($text);
                        $example = function($arg) use ($message) {
                            var_dump($arg . " " . $message);
                        };
                        $example = function /*test*/ ($arg) use ($message) {
                            var_dump($arg . " " . $message);
                        };
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class MethodTest1
                {
                    private $c; //

                    public function method444a()
                    {
                    }

                    /**
                     *
                     */
                    public function method444b()
                    {
                    }

                    //

                    public function method444c()
                    {
                    }

                    private $a;

                    public function method444d()
                    {
                    }

                    private $b;

                    //
                    public function method444e()
                    {
                    }

                    public function method444f()
                    {
                    }

                    private $d; //

                    public function method444f1()
                    {
                    }

                    /**/
                    public function method444g()
                    {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class MethodTest1
                {
                    private $c; //
                    public function method444a()
                    {
                    }
                    /**
                     *
                     */
                    public function method444b()
                    {
                    }

                    //


                    public function method444c()
                    {
                    }

                    private $a;
                    public function method444d()
                    {
                    }
                    private $b;
                    //
                    public function method444e()
                    {
                    }

                    public function method444f()
                    {
                    }

                    private $d; //
                    public function method444f1()
                    {
                    }

                    /**/
                    public function method444g()
                    {
                    }
                }
                EOD,
        ];

        // spaces between methods
        yield [
            <<<'EOD'
                <?php
                abstract class MethodTest3
                {
                    public function method021()
                    {
                    }

                    public static function method121()
                    {
                    }

                    abstract protected function method221();
                EOD.'	'.<<<'EOD'


                    final private function method321a()
                    {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                abstract class MethodTest3
                {
                    public function method021()
                    {
                    }

                    public static function method121()
                    {
                    }


                    abstract protected function method221();



                EOD."\n	".<<<'EOD'





                    final private function method321a()
                    {
                    }
                }
                EOD, ];

        // don't change correct code
        yield [
            <<<'EOD'
                <?php
                class SmallHelperException extends \Exception
                {
                    public function getId111()
                    {
                        return 1;
                    }

                    public function getMessage111()
                    {
                        return 'message';
                    }
                }

                class MethodTest123124124
                {
                    public function method111a(){}

                    public function method211a(){}
                }
                EOD,
        ];

        // do not touch function out of class scope
        yield [
            <<<'EOD'
                <?php
                function some0() {

                }
                class MethodTest4
                {
                    public function method122b()
                    {
                    }

                    public function method222b()
                    {
                    }
                }
                function some() {

                }
                function some2() {

                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php interface A {
                public function B1(); // allowed comment

                                public function C(); // allowed comment
                            }
                EOD,
            <<<'EOD'
                <?php interface A {public function B1(); // allowed comment
                                public function C(); // allowed comment
                            }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php class Foo {
                                var $a;

                                var $b;
                            }
                EOD,
            <<<'EOD'
                <?php class Foo {
                                var $a;
                                var $b;
                            }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                class A
                                {
                                    /**  1 */
                                    function A2() {}

                                    /**  2 */
                                    function B2() {}
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                class A
                                {

                                    /**  1 */
                                    function A2() {}
                                    /**  2 */
                                    function B2() {}
                                }
                EOD."\n            ",
        ];

        // do not touch well formatted traits
        yield [
            <<<'EOD'
                <?php
                trait OkTrait
                {
                    function getReturnTypeOk()
                    {
                    }

                    /**
                     *
                     */
                    function getReturnDescriptionOk()
                    {
                    }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                trait ezcReflectionReturnInfo {
                    public $x = 1;

                    protected function getA(){echo 1;}

                function getB(){echo 2;}

                    protected function getC(){echo 3;}

                /** Description */
                function getD(){echo 4;}

                    protected function getE(){echo 3;}

                private $a;

                function getF(){echo 4;}
                }
                EOD,
            <<<'EOD'
                <?php
                trait ezcReflectionReturnInfo {
                    public $x = 1;
                    protected function getA(){echo 1;}function getB(){echo 2;}
                    protected function getC(){echo 3;}/** Description */function getD(){echo 4;}
                    protected function getE(){echo 3;}private $a;function getF(){echo 4;}
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                trait SomeReturnInfo {
                    function getReturnType()
                    {
                    }

                    function getReturnDescription()
                    {
                    }

                 function getReturnDescription2()
                    {
                    }

                    abstract public function getWorld();
                }
                EOD,
            <<<'EOD'
                <?php
                trait SomeReturnInfo {
                    function getReturnType()
                    {
                    }
                    function getReturnDescription()
                    {
                    } function getReturnDescription2()
                    {
                    }

                    abstract public function getWorld();
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                interface TestInterface
                {
                    public function someInterfaceMethod4();

                    public function someInterfaceMethod5();

                    /**
                     * {@link}
                     */
                EOD.'           '.<<<'EOD'

                    public function someInterfaceMethod6();

                    public function someInterfaceMethod7();

                 public function someInterfaceMethod8();
                }
                EOD,
            <<<'EOD'
                <?php
                interface TestInterface
                {    public function someInterfaceMethod4();
                    public function someInterfaceMethod5();


                    /**
                     * {@link}
                     */
                EOD.'           '.<<<'EOD'

                    public function someInterfaceMethod6();


                    public function someInterfaceMethod7(); public function someInterfaceMethod8();
                }
                EOD,
        ];

        // do not touch well formatted interfaces
        yield [
            <<<'EOD'
                <?php
                interface TestInterfaceOK
                {
                    public function someMethod1();

                    public function someMethod2();
                }
                EOD,
        ];

        // method after trait use
        yield [
            <<<'EOD'
                <?php
                trait ezcReflectionReturnInfo {
                    function getReturnDescription() {}
                }
                class ezcReflectionMethod extends ReflectionMethod {
                    use ezcReflectionReturnInfo;

                function afterUseTrait(){}

                function afterUseTrait2(){}
                }
                EOD,
            <<<'EOD'
                <?php
                trait ezcReflectionReturnInfo {
                    function getReturnDescription() {}
                }
                class ezcReflectionMethod extends ReflectionMethod {
                    use ezcReflectionReturnInfo;function afterUseTrait(){}function afterUseTrait2(){}



                }
                EOD,
        ];

        yield 'multi line property' => [
            <<<'EOD'
                <?php class Foo
                {
                     private $prop = [
                         1 => true,
                         2 => false,
                     ];

                 // comment2
                     private $bar = 1;
                }
                EOD,
            <<<'EOD'
                <?php class Foo
                {
                     private $prop = [
                         1 => true,
                         2 => false,
                     ]; // comment2
                     private $bar = 1;
                }
                EOD,
            ['elements' => ['property' => 'one']],
        ];

        yield 'trait group import none' => [
            <<<'EOD'
                <?php class Foo
                {
                    use Ao;
                    use B0 { X0 as Y0;} // test
                    use A;
                    use B { X as Y;} // test
                    use Char;
                    use Bar {
                        __construct as barConstruct;
                        baz as barBaz;
                    }
                    use Dua;
                }
                EOD,
            <<<'EOD'
                <?php class Foo
                {
                    use Ao;

                    use B0 { X0 as Y0;} // test


                    use A;
                    use B { X as Y;} // test
                    use Char;

                    use Bar {
                        __construct as barConstruct;
                        baz as barBaz;
                    }
                    use Dua;
                }
                EOD,
            ['elements' => ['trait_import' => 'none']],
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    /** A */
                    private $email;

                    private $foo0; #0 /* test */
                    private $foo1; #1
                    private $foo2; /* @2 */
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    /** A */

                    private $email;

                    private $foo0; #0 /* test */

                    private $foo1; #1

                    private $foo2; /* @2 */
                }
                EOD,
            ['elements' => ['property' => 'none']],
        ];

        yield [
            <<<'EOD'
                <?php
                 class Sample
                {
                    /** @var int */
                    const FOO = 1;

                    /** @var int */
                    const BAR = 2;

                    const BAZ = 3;
                    const OTHER = 4;
                    const OTHER2 = 5;
                }
                EOD,
            <<<'EOD'
                <?php
                 class Sample
                {
                    /** @var int */
                    const FOO = 1;

                    /** @var int */
                    const BAR = 2;


                    const BAZ = 3;
                    const OTHER = 4;

                    const OTHER2 = 5;
                }
                EOD,
            ['elements' => ['const' => 'none']],
        ];

        yield 'multiple trait import 5954' => [
            <<<'EOD'
                <?php
                class Foo
                {
                    use Bar, Baz;
                }
                EOD,
            null,
            ['elements' => ['method' => 'one']],
        ];

        yield 'multiple trait import with method 5954' => [
            <<<'EOD'
                <?php
                class Foo
                {
                    use Bar, Baz;

                    public function f() {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    use Bar, Baz;


                    public function f() {}
                }
                EOD,
            ['elements' => ['method' => 'one']],
        ];

        yield 'trait group import 5843' => [
            <<<'EOD'
                <?php
                            class Foo
                {
                    use Ao;

                    use B0 { X0 as Y0;} // test

                    use A;

                    use B { X as Y;} // test

                    use Char;

                    use Bar {
                        __construct as barConstruct;
                        baz as barBaz;
                    }

                    use Dua;

                    public function aaa()
                    {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                            class Foo
                {
                    use Ao;
                    use B0 { X0 as Y0;} // test
                    use A;
                    use B { X as Y;} // test


                    use Char;
                    use Bar {
                        __construct as barConstruct;
                        baz as barBaz;
                    }
                    use Dua;
                    public function aaa()
                    {
                    }
                }
                EOD,
            ['elements' => ['method' => 'one', 'trait_import' => 'one']],
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    use SomeTrait1;

                    use SomeTrait2;

                    public function Bar(){}
                }

                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    use SomeTrait1;
                    use SomeTrait2;
                    public function Bar(){}
                }

                EOD,
            ['elements' => ['method' => 'one', 'trait_import' => 'one']],
        ];

        yield 'trait group import 5852' => [
            <<<'EOD'
                <?php
                class Foo
                {
                    use A;
                    use B;

                    /**
                     *
                     */
                     public function A(){}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    use A;

                    use B;

                    /**
                     *
                     */

                     public function A(){}
                }
                EOD,
            ['elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none']],
        ];

        yield [
            <<<'EOD'
                <?php
                abstract class Example
                {
                    use SomeTrait;
                    use AnotherTrait;

                    public $property;

                    abstract public function method(): void;
                }
                EOD,
            <<<'EOD'
                <?php
                abstract class Example
                {
                    use SomeTrait;
                    use AnotherTrait;
                    public $property;
                    abstract public function method(): void;
                }
                EOD,
            ['elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        private $a = null;

                                        public $b = 1;



                                        function A() {}
                                     }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        private $a = null;
                                        public $b = 1;



                                        function A() {}
                                     }
                EOD."\n                ",
            ['elements' => ['property' => 'one']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        private $a = null;
                                        public $b = 1;

                                        function A() {}
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        private $a = null;

                                        public $b = 1;

                                        function A() {}
                                    }
                EOD."\n                ",
            ['elements' => ['property' => 'none']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const A = 1;

                                        const THREE = ONE + self::TWO; /* test */ # test

                                        const B = 2;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {

                                        const A = 1;
                                        const THREE = ONE + self::TWO; /* test */ # test
                                        const B = 2;
                                    }
                EOD."\n                ",
            ['elements' => ['const' => 'one']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const A = 1;
                                        const THREE = ONE + self::TWO;
                                        const B = 2;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const A = 1;

                                        const THREE = ONE + self::TWO;

                                        const B = 2;
                                    }
                EOD."\n                ",
            ['elements' => ['const' => 'none']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        function D() {}

                                        function B4() {}
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        function D() {}
                                        function B4() {}
                                    }
                EOD."\n                ",
            ['elements' => ['method' => 'one']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        function A() {}
                                        function B() {}
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        function A() {}

                                        function B() {}
                                    }
                EOD."\n                ",
            ['elements' => ['method' => 'none']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        private $x;
                                        private $y;

                                        final function f1() {}

                                        final function f2() {}
                                     }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        private $x;
                                        private $y;
                                        final function f1() {}
                                        final function f2() {}
                                     }
                EOD."\n                ",
            ['elements' => ['property' => 'none', 'method' => 'one']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const FOO = 1;
                                        const BAR = 2;

                                        function f1() {}

                                        function f2() {}
                                     }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const FOO = 1;
                                        const BAR = 2;
                                        function f1() {}
                                        function f2() {}
                                     }
                EOD."\n                ",
            ['elements' => ['const' => 'none', 'method' => 'one']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const FOO = 1;
                                        const BAR = 2;

                                        public function f1() {}

                                        public function f2() {}
                                     }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const FOO = 1;
                                        const BAR = 2;
                                        public function f1() {}
                                        public function f2() {}
                                     }
                EOD."\n                ",
            ['elements' => ['const' => 'none', 'method' => 'one']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const B = 2;
                                        const FOO = 1;
                                        const BAR = 2;

                                        /** @var int */
                                        const BAZ = 3;

                                        /** @var int */
                                        const NEW = 4;

                                        /** @var int */
                                        const A = 5;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        const B = 2;
                                        const FOO = 1;

                                        const BAR = 2;

                                        /** @var int */
                                        const BAZ = 3;
                                        /** @var int */
                                        const NEW = 4;
                                        /** @var int */
                                        const A = 5;
                                    }
                EOD."\n                ",
            ['elements' => ['const' => 'only_if_meta']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class B
                                    {
                                        public $foo;

                                        /** @var string */
                                        public $bar;
                                        public $baz;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class B
                                    {
                                        public $foo;
                                        /** @var string */
                                        public $bar;

                                        public $baz;
                                    }
                EOD."\n                ",
            ['elements' => ['property' => 'only_if_meta']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class C
                                    {
                                        public function f1() {}
                                        public function f2() {}
                                        public function f3() {}

                                        /** @return string */
                                        public function f4() {}
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class C
                                    {
                                        public function f1() {}

                                        public function f2() {}

                                        public function f3() {}
                                        /** @return string */
                                        public function f4() {}
                                    }
                EOD."\n                ",
            ['elements' => ['method' => 'only_if_meta']],
        ];

        yield [
            <<<'EOD'
                <?php
                                class Sample
                                {
                                    /** @var int */
                                    const ART = 1;
                                    const SCIENCE = 2;

                                    /** @var string */
                                    public $a;

                                    /** @var int */
                                    public $b;
                                    public $c;

                                    /**
                                     * @param string $a
                                     * @param int $b
                                     * @param int $c
                                     */
                                    public function __construct($a, $b, $c) {}
                                    public function __destruct() {}
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                class Sample
                                {
                                    /** @var int */
                                    const ART = 1;

                                    const SCIENCE = 2;
                                    /** @var string */
                                    public $a;
                                    /** @var int */
                                    public $b;

                                    public $c;

                                    /**
                                     * @param string $a
                                     * @param int $b
                                     * @param int $c
                                     */
                                    public function __construct($a, $b, $c) {}

                                    public function __destruct() {}
                                }
                EOD."\n                ",
            ['elements' => ['const' => 'only_if_meta', 'property' => 'only_if_meta', 'method' => 'only_if_meta']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        use A;
                                        use B;

                                        private $a = null;
                                        public $b = 1;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        use A;

                                        use B;

                                        private $a = null;

                                        public $b = 1;
                                    }
                EOD."\n                ",
            ['elements' => ['property' => 'none', 'trait_import' => 'none']],
        ];

        yield [
            <<<'EOD'
                <?php
                                class Foo {
                    public function H1(){}

                    /**  */
                    public const BAR = 123;

                    /**  */
                    private const BAZ = "a";
                                }
                EOD,
            <<<'EOD'
                <?php
                                class Foo {



                    public function H1(){}


                    /**  */
                    public const BAR = 123;
                    /**  */
                    private const BAZ = "a";


                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                            class Foo {
                                private ?int $foo;

                                protected string $bar;

                                public iterable $baz;

                                var ? Foo\Bar $qux;
                            }
                EOD,
            <<<'EOD'
                <?php
                            class Foo {
                                private ?int $foo;
                                protected string $bar;
                                public iterable $baz;
                                var ? Foo\Bar $qux;
                            }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                            class Foo {
                                private array $foo;

                                private array $bar;
                            }
                EOD,
            <<<'EOD'
                <?php
                            class Foo {
                                private array $foo;
                                private array $bar;
                            }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                            class Entity
                            {
                                /**
                                 * @ORM\Column(name="one", type="text")
                                 */
                                private string $one;

                                /**
                                 * @ORM\Column(name="two", type="text")
                                 */
                                private string $two;
                                private string $three;
                                private string $four;
                                private string $five;
                            }
                EOD,
            <<<'EOD'
                <?php
                            class Entity
                            {
                                /**
                                 * @ORM\Column(name="one", type="text")
                                 */
                                private string $one;
                                /**
                                 * @ORM\Column(name="two", type="text")
                                 */
                                private string $two;

                                private string $three;

                                private string $four;

                                private string $five;
                            }
                EOD,
            ['elements' => ['property' => 'only_if_meta']],
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    use SomeTrait1;
                    use SomeTrait2;

                    public function Bar(){}
                }

                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    use SomeTrait1;

                    use SomeTrait2;
                    public function Bar(){}
                }

                EOD,
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'attributes' => [
            <<<'EOD'
                <?php
                class User1
                {
                    #[ORM\Id, ORM\Column("integer"), ORM\GeneratedValue]
                    private $id;

                    #[ORM\Column("string", ORM\Column::UNIQUE)]
                    #[Assert\String()]
                    #[Assert\Email(["message" => "The email {{ value }} is not a valid email."])]
                    private $email;

                    #[Assert\String()]
                    private $name;
                }
                EOD,
            <<<'EOD'
                <?php
                class User1
                {

                    #[ORM\Id, ORM\Column("integer"), ORM\GeneratedValue]


                    private $id;
                    #[ORM\Column("string", ORM\Column::UNIQUE)]
                    #[Assert\String()]
                    #[Assert\Email(["message" => "The email {{ value }} is not a valid email."])]
                    private $email;


                    #[Assert\String()]


                    private $name;



                }
                EOD,
        ];

        yield 'attributes minimal' => [
            <<<'EOD'
                <?php
                class User2{
                #[ORM\Id, ORM\Column("integer"), ORM\GeneratedValue]
                 private $id;
                }
                EOD,
            <<<'EOD'
                <?php
                class User2{#[ORM\Id, ORM\Column("integer"), ORM\GeneratedValue] private $id;}
                EOD,
        ];

        yield 'attribute block' => [
            <<<'EOD'
                <?php
                class User3
                {
                    private $id;

                    #[ORM\Column("string")]
                    #[Assert\Email(["message" => "Foo"])]
                 private $email;
                }
                EOD,

            <<<'EOD'
                <?php
                class User3
                {
                    private $id;
                    #[ORM\Column("string")]
                    #[Assert\Email(["message" => "Foo"])] private $email;
                }
                EOD,
        ];

        yield 'constructor property promotion' => [
            <<<'EOD'
                <?php
                            class Foo {
                                private array $foo;

                                private array $bar;

                                public function __construct(
                                    public float $x = 0.0,
                                    protected float $y = 0.0,
                                    private float $z = 0.0,
                                ) {}
                            }
                EOD,
            <<<'EOD'
                <?php
                            class Foo {
                                private array $foo;
                                private array $bar;
                                public function __construct(
                                    public float $x = 0.0,
                                    protected float $y = 0.0,
                                    private float $z = 0.0,
                                ) {}
                            }
                EOD,
        ];

        yield 'typed properties' => [
            <<<'EOD'
                <?php
                            class Foo {
                                private static int | float | null $a;

                                private static int | float | null $b;

                                private int | float | null $c;

                                private int | float | null $d;
                            }
                EOD,
            <<<'EOD'
                <?php
                            class Foo {
                                private static int | float | null $a;
                                private static int | float | null $b;
                                private int | float | null $c;
                                private int | float | null $d;
                            }
                EOD,
        ];

        yield 'attributes with conditional spacing' => [
            <<<'EOD'
                <?php
                class User
                {
                    private $id;

                    #[Assert\String()]
                    private $name;
                    private $email;
                }

                EOD,
            <<<'EOD'
                <?php
                class User
                {

                    private $id;
                    #[Assert\String()]
                    private $name;

                    private $email;
                }

                EOD,
            ['elements' => ['property' => 'only_if_meta']],
        ];

        yield 'mixed attributes and phpdoc with conditional spacing' => [
            <<<'EOD'
                <?php
                class User
                {
                    private $id;

                    /** @var string */
                    #[Assert\Email(["message" => "Foo"])]
                    private $email;

                    #[Assert\String()]
                    #[ORM\Column()]
                    private $place;

                    #[ORM\Column()]
                    /** @var string */
                    private $hash;

                    /** @var string **/
                    #[ORM\Column()]
                    /** @internal */
                    private $updatedAt;
                }

                EOD,
            <<<'EOD'
                <?php
                class User
                {

                    private $id;
                    /** @var string */
                    #[Assert\Email(["message" => "Foo"])]
                    private $email;
                    #[Assert\String()]
                    #[ORM\Column()]
                    private $place;
                    #[ORM\Column()]
                    /** @var string */
                    private $hash;


                    /** @var string **/
                    #[ORM\Column()]
                    /** @internal */
                    private $updatedAt;
                }

                EOD,
            ['elements' => ['property' => 'only_if_meta']],
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    #[Assert\Email(["message" => "Foo"])]
                    private $email;

                    private $foo1; #1
                    private $foo2; /* @2 */
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    #[Assert\Email(["message" => "Foo"])]

                    private $email;

                    private $foo1; #1

                    private $foo2; /* @2 */
                }
                EOD,
            ['elements' => ['property' => 'none']],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php class A {
                    public int $a0;

                    public readonly int $a1;

                    readonly public int $a2;

                    readonly int $a3;

                    public int $a4;
                }
                EOD,
            <<<'EOD'
                <?php class A {
                    public int $a0;
                    public readonly int $a1;
                    readonly public int $a2;
                    readonly int $a3;
                    public int $a4;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class Foo
                {
                    final public const B1 = "1";

                    public final const B2 = "2";

                    final const B3 = "3";
                }

                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    final public const B1 = "1";
                    public final const B2 = "2";


                    final const B3 = "3";


                }

                EOD,
        ];

        yield 'intersection properties' => [
            <<<'EOD'
                <?php
                            class Foo {
                                private static Bar & Something & Baz $a;

                                private static Bar & Something & Baz $b;

                                private Bar & Something & Baz $c;

                                private Bar & Something & Baz $d;
                            }
                EOD,
            <<<'EOD'
                <?php
                            class Foo {
                                private static Bar & Something & Baz $a;
                                private static Bar & Something & Baz $b;
                                private Bar & Something & Baz $c;
                                private Bar & Something & Baz $d;
                            }
                EOD,
        ];

        $input = <<<'EOD'
            <?php
            enum Cards: string
            {
                protected const Deck = "d.d";



                protected const Pack = "p.p";

                case Hearts = "H";


                case Spades = "S";




                case Diamonds = "D";


                case Clubs = "C";
                protected function test() {
                    echo 1;
                }


                protected function test2() {
                    echo 2;
                }
            }
            EOD."\n            ";

        yield [
            <<<'EOD'
                <?php
                enum Cards: string
                {
                    protected const Deck = "d.d";

                    protected const Pack = "p.p";

                    case Hearts = "H";

                    case Spades = "S";

                    case Diamonds = "D";

                    case Clubs = "C";

                    protected function test() {
                        echo 1;
                    }

                    protected function test2() {
                        echo 2;
                    }
                }
                EOD."\n            ",
            $input,
            ['elements' => [
                'const' => 'one',
                'method' => 'one',
                'case' => 'one',
            ]],
        ];

        yield [
            <<<'EOD'
                <?php
                enum Cards: string
                {
                    protected const Deck = "d.d";
                    protected const Pack = "p.p";

                    case Hearts = "H";
                    case Spades = "S";
                    case Diamonds = "D";
                    case Clubs = "C";

                    protected function test() {
                        echo 1;
                    }

                    protected function test2() {
                        echo 2;
                    }
                }
                EOD."\n            ",
            $input,
            ['elements' => [
                'const' => 'none',
                'method' => 'one',
                'case' => 'none',
            ]],
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                            trait Foo {
                                const Bar = 1;

                                const Baz = 2;
                            }
                EOD,
            <<<'EOD'
                <?php
                            trait Foo {
                                const Bar = 1;
                                const Baz = 2;
                            }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                            class X
                            {
                                private A|(B&C) $propertyName;
                            }
                EOD,
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php\r\nclass SomeClass\r\n{\r\n    // comment\n\n    public function echoA()\r\n    {\r\n        echo 'a';\r\n    }\r\n}\r\n",
            "<?php\r\nclass SomeClass\r\n{\r\n    // comment\n\n\n    public function echoA()\r\n    {\r\n        echo 'a';\r\n    }\r\n}\r\n",
        ];

        yield [
            "<?php\r\nclass SomeClass\r\n{\r\n    // comment\r\n\r\n    public function echoA()\r\n    {\r\n        echo 'a';\r\n    }\r\n}\r\n",
            "<?php\r\nclass SomeClass\r\n{\r\n    // comment\r\n\r\n\r\n    public function echoA()\r\n    {\r\n        echo 'a';\r\n    }\r\n}\r\n",
        ];
    }

    /**
     * @param array<mixed> $elements
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfiguration(array $elements): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->fixer->configure(['elements' => $elements]);
    }

    public static function provideInvalidConfigurationCases(): iterable
    {
        yield 'numeric keys' => [['method', 'property']];

        yield 'wrong key name' => [['methods' => 'one']];

        yield 'wrong key value' => [['method' => 'two']];
    }

    /**
     * @dataProvider provideCommentBlockStartDetectionCases
     */
    public function testCommentBlockStartDetection(int $expected, string $code, int $index): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($code);
        $method = new \ReflectionMethod($this->fixer, 'findCommentBlockStart');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $index, 0);
        self::assertSame(
            $expected,
            $result,
            sprintf('Expected index %d (%s) got index %d (%s).', $expected, $tokens[$expected]->toJson(), $result, $tokens[$result]->toJson())
        );
    }

    public static function provideCommentBlockStartDetectionCases(): iterable
    {
        yield [
            4,
            <<<'EOD'
                <?php
                                    //ui

                                    //j1
                                    //k2
                EOD."\n                ",
            6,
        ];

        yield [
            4,
            <<<'EOD'
                <?php
                                    //ui

                                    //j1
                                    //k2
                EOD."\n                ",
            5,
        ];

        yield [
            4,
            <<<'EOD'
                <?php
                                    /**/

                                    //j1
                                    //k2
                EOD."\n                ",
            6,
        ];

        yield [
            4,
            <<<'EOD'
                <?php
                                    $a;//j
                                    //k
                EOD."\n                ",
            6,
        ];

        yield [
            2,
            <<<'EOD'
                <?php
                                    //a
                EOD."\n                ",
            2,
        ];

        yield [
            2,
            <<<'EOD'
                <?php
                                    //b
                                    //c
                EOD."\n                ",
            2,
        ];

        yield [
            2,
            <<<'EOD'
                <?php
                                    //d
                                    //e
                EOD."\n                ",
            4,
        ];

        yield [
            2,
            <<<'EOD'
                <?php
                                    /**/
                                    //f
                                    //g
                                    //h
                EOD."\n                ",
            8,
        ];
    }
}
