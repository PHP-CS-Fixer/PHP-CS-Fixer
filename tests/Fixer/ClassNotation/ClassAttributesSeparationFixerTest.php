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
     * @dataProvider provideFixCases
     */
    public function testFixCases(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield [
            '<?php
class Sample
{
private $a; // foo

    /** second in a hour */
    private $b;
}
',
            '<?php
class Sample
{private $a; // foo
    /** second in a hour */
    private $b;
}
',
        ];

        yield 'empty class' => [
            '<?php class Foo {}',
        ];

        yield 'simple top class' => [
            '<?php class A {
public function Foo(){}
}',
            '<?php class A {public function Foo(){}}',
        ];

        yield 'comment' => [
            '<?php class A {
/* function comment */
public function Bar(){}
}',
            '<?php class A {/* function comment */public function Bar(){}
}',
        ];

        yield 'comment, multiple lines' => [
            '<?php class A {
/* some comment */

public function Bar(){}
}',
            '<?php class A {
/* some comment */



public function Bar(){}
}',
        ];

        yield 'simple PHPDoc case' => [
            '<?php class Foo
{
/** Doc 1 */
public function A(){}

    /** Doc 2 */
    public function B(){}
}',
            '<?php class Foo
{/** Doc 1 */public function A(){}

    /** Doc 2 */

    public function B(){}
}',
        ];

        yield 'add a newline at the end of a class with trait group' => [
            '<?php class A
{
    use Bar {
        __construct as barConstruct;
        baz as barBaz;
    }
}',
            '<?php class A
{
    use Bar {
        __construct as barConstruct;
        baz as barBaz;
    }}',
        ];

        yield 'add a newline at the end of a class with trait' => [
            '<?php class A
{
    use A\B\C;
}',
            '<?php class A
{
    use A\B\C;}',
        ];

        yield 'removes extra lines at the end of an interface' => [
            '<?php interface F
{
    public function A();
}',
            '<?php interface F
{
    public function A();


}',
        ];

        yield 'removes extra lines at the end of an abstract class' => [
            '<?php abstract class F
{
    public abstract function A();
}',
            '<?php abstract class F
{
    public abstract function A();


}',
        ];

        yield 'add a newline at the end of a class' => [
            '<?php class A
{
    public function A(){}
}',
            '<?php class A
{
    public function A(){}}',
        ];

        yield 'add a newline at the end of a class: with comments' => [
            '<?php class A
{
    public const A = 1; /* foo */ /* bar */
}',
            '<?php class A
{
    public const A = 1; /* foo */ /* bar */}',
        ];

        yield 'add a newline at the end of a class: with comments with trailing space' => [
            '<?php class A
{
    public const A = 1; /* foo */ /* bar */
   }',
            '<?php class A
{
    public const A = 1; /* foo */ /* bar */   }',
        ];

        $to = $from = '<?php ';

        for ($i = 0; $i < 15; ++$i) {
            $from .= sprintf('class A%d{public function GA%d(){return new class {public function B6B%d(){}};}public function otherFunction%d(){}}', $i, $i, $i, $i);
            $to .= sprintf("class A%d{\npublic function GA%d(){return new class {\npublic function B6B%d(){}\n};}\n\npublic function otherFunction%d(){}\n}", $i, $i, $i, $i);
        }

        yield from [
            [$to, $from],
            [
                '<?php $a = new class {
                public function H(){}

                public function B7(){}

                private function C(){}
                };',
                '<?php $a = new class {
                public function H(){}
                public function B7(){}
                private function C(){}
                };',
            ],
            [
                '<?php
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
                ',
                '<?php
                    class A
                    {public function getFilter()
                        {
                            return new class () implements FilterInterface {private $d = 123;
                                public function pass($a, $b) {
                                    echo $a;
                                }
                                public $e = 5;};}



                    }
                ',
            ],
        ];
    }

    /**
     * @param array<mixed> $elements
     *
     * @dataProvider provideInvalidElementsCases
     */
    public function testInvalidElements(array $elements): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->fixer->configure(['elements' => $elements]);
    }

    public static function provideInvalidElementsCases(): iterable
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
        static::assertSame(
            $expected,
            $result,
            sprintf('Expected index %d (%s) got index %d (%s).', $expected, $tokens[$expected]->toJson(), $result, $tokens[$result]->toJson())
        );
    }

    public function provideCommentBlockStartDetectionCases(): array
    {
        return [
            [
                4,
                '<?php
                    //ui

                    //j1
                    //k2
                ',
                6,
            ],
            [
                4,
                '<?php
                    //ui

                    //j1
                    //k2
                ',
                5,
            ],
            [
                4,
                '<?php
                    /**/

                    //j1
                    //k2
                ',
                6,
            ],
            [
                4,
                '<?php
                    $a;//j
                    //k
                ',
                6,
            ],
            [
                2,
                '<?php
                    //a
                ',
                2,
            ],
            [
                2,
                '<?php
                    //b
                    //c
                ',
                2,
            ],
            [
                2,
                '<?php
                    //d
                    //e
                ',
                4,
            ],
            [
                2,
                '<?php
                    /**/
                    //f
                    //g
                    //h
                ',
                8,
            ],
        ];
    }

    /**
     * @dataProvider provideFixClassesCases
     */
    public function testFixClasses(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixClassesCases(): array
    {
        $cases = [];

        $cases[] = ['<?php
class SomeClass1
{
    // This comment
    // is multiline.
    public function echoA()
    {
        echo "a";
    }
}
'];

        $cases[] = [
            '<?php
class SomeClass2
{
    // This comment
    /* is multiline. */
public function echoA()
    {
        echo "a";
    }
}
            ',
            '<?php
class SomeClass2
{
    // This comment
    /* is multiline. */public function echoA()
    {
        echo "a";
    }
}
            ',
        ];

        $cases[] = [
            '<?php
class SomeClass3
{
    // This comment
    // is multiline.

    public function echoA()
    {
        echo "a";
    }
}
', ];

        $cases[] = [
            '<?php
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
',
            '<?php
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
', ];

        $cases[] = ['<?php
class SomeClass
{
    // comment
    public function echoA()
    {
        echo "a";
    }
}
'];

        $cases[] = ['<?php
class SomeClass
{
    // This comment
    // is multiline.
    public function echoA()
    {
        echo "a";
    }
}
'];

        $cases[] = [
            '<?php
class SomeClass
{
    // comment

    public function echoA()
    {
        echo "a";
    }
}
',
            '<?php
class SomeClass
{
    // comment


    public function echoA()
    {
        echo "a";
    }
}
',
        ];

        $cases[] = [
            '<?php
class SomeClass
{
    /* comment */
public function echoB()
    {
        echo "a";
    }
}
',
            '<?php
class SomeClass
{
    /* comment */public function echoB()
    {
        echo "a";
    }
}
',
        ];

        $cases[] = [
            '<?php
class SomeClass
{
    /* comment */
 public function echoC()
    {
        echo "a";
    }
}
',
            '<?php
class SomeClass
{
    /* comment */ public function echoC()
    {
        echo "a";
    }
}
',
        ];

        $cases[] = [
            '<?php
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
function some2(){ echo 2;}',
            '<?php
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
function some2(){ echo 2;}',
        ];

        $cases[] = [
            '<?php

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
',
        ];

        // do not touch anonymous functions (since PHP doesn't allow
        // for class attributes being functions :(, we only have to test
        // those used within methods)
        $cases[] = [
            '<?php
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
}',
        ];

        $cases[] = [
            '<?php
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
}',
            '<?php
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
}',
        ];

        // spaces between methods
        $cases[] = [
            '<?php
abstract class MethodTest3
{
    public function method021()
    {
    }

    public static function method121()
    {
    }

    abstract protected function method221();	'.'

    final private function method321a()
    {
    }
}',
            '<?php
abstract class MethodTest3
{
    public function method021()
    {
    }

    public static function method121()
    {
    }


    abstract protected function method221();



	'.'




    final private function method321a()
    {
    }
}', ];
        // don't change correct code
        $cases[] = [
            '<?php
class SmallHelperException extends \Exception
{
    public function getId111()
    {
        return 1;
    }

    public function getMessage111()
    {
        return \'message\';
    }
}

class MethodTest123124124
{
    public function method111a(){}

    public function method211a(){}
}',
        ];

        // do not touch function out of class scope
        $cases[] = [
            '<?php
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
',
        ];

        $cases[] = [
            '<?php interface A {
public function B1(); // allowed comment

                public function C(); // allowed comment
            }',
            '<?php interface A {public function B1(); // allowed comment
                public function C(); // allowed comment
            }',
        ];
        $cases[] = [
            '<?php class Foo {
                var $a;

                var $b;
            }',
            '<?php class Foo {
                var $a;
                var $b;
            }',
        ];

        $cases[] = [
            '<?php
                class A
                {
                    /**  1 */
                    function A2() {}

                    /**  2 */
                    function B2() {}
                }
            ',
            '<?php
                class A
                {

                    /**  1 */
                    function A2() {}
                    /**  2 */
                    function B2() {}
                }
            ',
        ];

        return $cases;
    }

    /**
     * @dataProvider provideFixTraitsCases
     */
    public function testFixTraits(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixTraitsCases(): array
    {
        $cases = [];

        // do not touch well formatted traits
        $cases[] = [
            '<?php
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
}',
        ];

        $cases[] = [
            '<?php
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
}',
            '<?php
trait ezcReflectionReturnInfo {
    public $x = 1;
    protected function getA(){echo 1;}function getB(){echo 2;}
    protected function getC(){echo 3;}/** Description */function getD(){echo 4;}
    protected function getE(){echo 3;}private $a;function getF(){echo 4;}
}',
        ];

        $cases[] = [
            '<?php
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
}',
            '<?php
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
}',
        ];

        return $cases;
    }

    /**
     * @dataProvider provideFixInterfaceCases
     */
    public function testFixInterface(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixInterfaceCases(): array
    {
        $cases = [];
        $cases[] = [
            '<?php
interface TestInterface
{
    public function someInterfaceMethod4();

    public function someInterfaceMethod5();

    /**
     * {@link}
     */           '.'
    public function someInterfaceMethod6();

    public function someInterfaceMethod7();

 public function someInterfaceMethod8();
}',
            '<?php
interface TestInterface
{    public function someInterfaceMethod4();
    public function someInterfaceMethod5();


    /**
     * {@link}
     */           '.'
    public function someInterfaceMethod6();


    public function someInterfaceMethod7(); public function someInterfaceMethod8();
}',
        ];

        // do not touch well formatted interfaces
        $cases[] = [
            '<?php
interface TestInterfaceOK
{
    public function someMethod1();

    public function someMethod2();
}',
        ];

        // method after trait use
        $cases[] = [
            '<?php
trait ezcReflectionReturnInfo {
    function getReturnDescription() {}
}
class ezcReflectionMethod extends ReflectionMethod {
    use ezcReflectionReturnInfo;

function afterUseTrait(){}

function afterUseTrait2(){}
}',
            '<?php
trait ezcReflectionReturnInfo {
    function getReturnDescription() {}
}
class ezcReflectionMethod extends ReflectionMethod {
    use ezcReflectionReturnInfo;function afterUseTrait(){}function afterUseTrait2(){}



}',
        ];

        return $cases;
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases(): array
    {
        return [
            [
                "<?php\r\nclass SomeClass\r\n{\r\n    // comment\n\n    public function echoA()\r\n    {\r\n        echo 'a';\r\n    }\r\n}\r\n",
                "<?php\r\nclass SomeClass\r\n{\r\n    // comment\n\n\n    public function echoA()\r\n    {\r\n        echo 'a';\r\n    }\r\n}\r\n",
            ],
            [
                "<?php\r\nclass SomeClass\r\n{\r\n    // comment\r\n\r\n    public function echoA()\r\n    {\r\n        echo 'a';\r\n    }\r\n}\r\n",
                "<?php\r\nclass SomeClass\r\n{\r\n    // comment\r\n\r\n\r\n    public function echoA()\r\n    {\r\n        echo 'a';\r\n    }\r\n}\r\n",
            ],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideConfigCases
     */
    public function testWithConfig(string $expected, ?string $input, array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideConfigCases(): array
    {
        return [
            'multi line property' => [
                '<?php class Foo
{
     private $prop = [
         1 => true,
         2 => false,
     ];

 // comment2
     private $bar = 1;
}',
                '<?php class Foo
{
     private $prop = [
         1 => true,
         2 => false,
     ]; // comment2
     private $bar = 1;
}',
                ['elements' => ['property' => 'one']],
            ],
            'trait group import none' => [
                '<?php class Foo
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
}',
                '<?php class Foo
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
}',
                ['elements' => ['trait_import' => 'none']],
            ],
            [
                '<?php
class Foo
{
    /** A */
    private $email;

    private $foo0; #0 /* test */
    private $foo1; #1
    private $foo2; /* @2 */
}',
                '<?php
class Foo
{
    /** A */

    private $email;

    private $foo0; #0 /* test */

    private $foo1; #1

    private $foo2; /* @2 */
}',
                ['elements' => ['property' => 'none']],
            ],
            [
                '<?php
 class Sample
{
    /** @var int */
    const FOO = 1;

    /** @var int */
    const BAR = 2;

    const BAZ = 3;
    const OTHER = 4;
    const OTHER2 = 5;
}',
                '<?php
 class Sample
{
    /** @var int */
    const FOO = 1;

    /** @var int */
    const BAR = 2;


    const BAZ = 3;
    const OTHER = 4;

    const OTHER2 = 5;
}',
                ['elements' => ['const' => 'none']],
            ],
            'multiple trait import 5954' => [
                '<?php
class Foo
{
    use Bar, Baz;
}',
                null,
                ['elements' => ['method' => 'one']],
            ],
            'multiple trait import with method 5954' => [
                '<?php
class Foo
{
    use Bar, Baz;

    public function f() {}
}',
                '<?php
class Foo
{
    use Bar, Baz;


    public function f() {}
}',
                ['elements' => ['method' => 'one']],
            ],
            'trait group import 5843' => [
                '<?php
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
}',
                '<?php
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
}',
                ['elements' => ['method' => 'one', 'trait_import' => 'one']],
            ],
            [
                '<?php
class Foo
{
    use SomeTrait1;

    use SomeTrait2;

    public function Bar(){}
}
',
                '<?php
class Foo
{
    use SomeTrait1;
    use SomeTrait2;
    public function Bar(){}
}
',
                ['elements' => ['method' => 'one', 'trait_import' => 'one']],
            ],
            'trait group import 5852' => [
                '<?php
class Foo
{
    use A;
    use B;

    /**
     *
     */
     public function A(){}
}',
                '<?php
class Foo
{
    use A;

    use B;

    /**
     *
     */

     public function A(){}
}',
                ['elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none']],
            ],
            [
                '<?php
abstract class Example
{
    use SomeTrait;
    use AnotherTrait;

    public $property;

    abstract public function method(): void;
}',
                '<?php
abstract class Example
{
    use SomeTrait;
    use AnotherTrait;
    public $property;
    abstract public function method(): void;
}',
                ['elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one']],
            ],
            [
                '<?php
                    class A
                    {
                        private $a = null;

                        public $b = 1;



                        function A() {}
                     }
                ',
                '<?php
                    class A
                    {
                        private $a = null;
                        public $b = 1;



                        function A() {}
                     }
                ',
                ['elements' => ['property' => 'one']],
            ],
            [
                '<?php
                    class A
                    {
                        private $a = null;
                        public $b = 1;

                        function A() {}
                    }
                ',
                '<?php
                    class A
                    {
                        private $a = null;

                        public $b = 1;

                        function A() {}
                    }
                ',
                ['elements' => ['property' => 'none']],
            ],
            [
                '<?php
                    class A
                    {
                        const A = 1;

                        const THREE = ONE + self::TWO; /* test */ # test

                        const B = 2;
                    }
                ',
                '<?php
                    class A
                    {

                        const A = 1;
                        const THREE = ONE + self::TWO; /* test */ # test
                        const B = 2;
                    }
                ',
                ['elements' => ['const' => 'one']],
            ],
            [
                '<?php
                    class A
                    {
                        const A = 1;
                        const THREE = ONE + self::TWO;
                        const B = 2;
                    }
                ',
                '<?php
                    class A
                    {
                        const A = 1;

                        const THREE = ONE + self::TWO;

                        const B = 2;
                    }
                ',
                ['elements' => ['const' => 'none']],
            ],
            [
                '<?php
                    class A
                    {
                        function D() {}

                        function B4() {}
                    }
                ',
                '<?php
                    class A
                    {
                        function D() {}
                        function B4() {}
                    }
                ',
                ['elements' => ['method' => 'one']],
            ],
            [
                '<?php
                    class A
                    {
                        function A() {}
                        function B() {}
                    }
                ',
                '<?php
                    class A
                    {
                        function A() {}

                        function B() {}
                    }
                ',
                ['elements' => ['method' => 'none']],
            ],
            [
                '<?php
                    class A
                    {
                        private $x;
                        private $y;

                        final function f1() {}

                        final function f2() {}
                     }
                ',
                '<?php
                    class A
                    {
                        private $x;
                        private $y;
                        final function f1() {}
                        final function f2() {}
                     }
                ',
                ['elements' => ['property' => 'none', 'method' => 'one']],
            ],
            [
                '<?php
                    class A
                    {
                        const FOO = 1;
                        const BAR = 2;

                        function f1() {}

                        function f2() {}
                     }
                ',
                '<?php
                    class A
                    {
                        const FOO = 1;
                        const BAR = 2;
                        function f1() {}
                        function f2() {}
                     }
                ',
                ['elements' => ['const' => 'none', 'method' => 'one']],
            ],
            [
                '<?php
                    class A
                    {
                        const FOO = 1;
                        const BAR = 2;

                        public function f1() {}

                        public function f2() {}
                     }
                ',
                '<?php
                    class A
                    {
                        const FOO = 1;
                        const BAR = 2;
                        public function f1() {}
                        public function f2() {}
                     }
                ',
                ['elements' => ['const' => 'none', 'method' => 'one']],
            ],
            [
                '<?php
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
                ',
                '<?php
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
                ',
                ['elements' => ['const' => 'only_if_meta']],
            ],
            [
                '<?php
                    class B
                    {
                        public $foo;

                        /** @var string */
                        public $bar;
                        public $baz;
                    }
                ',
                '<?php
                    class B
                    {
                        public $foo;
                        /** @var string */
                        public $bar;

                        public $baz;
                    }
                ',
                ['elements' => ['property' => 'only_if_meta']],
            ],
            [
                '<?php
                    class C
                    {
                        public function f1() {}
                        public function f2() {}
                        public function f3() {}

                        /** @return string */
                        public function f4() {}
                    }
                ',
                '<?php
                    class C
                    {
                        public function f1() {}

                        public function f2() {}

                        public function f3() {}
                        /** @return string */
                        public function f4() {}
                    }
                ',
                ['elements' => ['method' => 'only_if_meta']],
            ],
            [
                '<?php
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
                ',
                '<?php
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
                ',
                ['elements' => ['const' => 'only_if_meta', 'property' => 'only_if_meta', 'method' => 'only_if_meta']],
            ],
            [
                '<?php
                    class A
                    {
                        use A;
                        use B;

                        private $a = null;
                        public $b = 1;
                    }
                ',
                '<?php
                    class A
                    {
                        use A;

                        use B;

                        private $a = null;

                        public $b = 1;
                    }
                ',
                ['elements' => ['property' => 'none', 'trait_import' => 'none']],
            ],
        ];
    }

    /**
     * @dataProvider provideFix71Cases
     */
    public function testFix71(string $expected, string $input): void
    {
        $this->fixer->configure([
            'elements' => ['method' => 'one', 'const' => 'one'],
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFix71Cases(): array
    {
        return [
            [
                '<?php
                class Foo {
    public function H1(){}

    /**  */
    public const BAR = 123;

    /**  */
    private const BAZ = "a";
                }',
                '<?php
                class Foo {



    public function H1(){}


    /**  */
    public const BAR = 123;
    /**  */
    private const BAZ = "a";


                }',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix74Cases
     */
    public function testFix74(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideFix74Cases(): iterable
    {
        yield [
            '<?php
            class Foo {
                private ?int $foo;

                protected string $bar;

                public iterable $baz;

                var ? Foo\Bar $qux;
            }',
            '<?php
            class Foo {
                private ?int $foo;
                protected string $bar;
                public iterable $baz;
                var ? Foo\Bar $qux;
            }',
        ];

        yield [
            '<?php
            class Foo {
                private array $foo;

                private array $bar;
            }',
            '<?php
            class Foo {
                private array $foo;
                private array $bar;
            }',
        ];

        yield [
            '<?php
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
            }',
            '<?php
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
            }',
            ['elements' => ['property' => 'only_if_meta']],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideFixPhp80Cases(): iterable
    {
        yield 'attributes' => [
            '<?php
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
}',
            '<?php
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



}',
        ];

        yield 'attributes minimal' => [
            '<?php
class User2{
#[ORM\Id, ORM\Column("integer"), ORM\GeneratedValue]
 private $id;
}',
            '<?php
class User2{#[ORM\Id, ORM\Column("integer"), ORM\GeneratedValue] private $id;}',
        ];

        yield 'attribute block' => [
            '<?php
class User3
{
    private $id;

    #[ORM\Column("string")]
    #[Assert\Email(["message" => "Foo"])]
 private $email;
}',

            '<?php
class User3
{
    private $id;
    #[ORM\Column("string")]
    #[Assert\Email(["message" => "Foo"])] private $email;
}',
        ];

        yield 'constructor property promotion' => [
            '<?php
            class Foo {
                private array $foo;

                private array $bar;

                public function __construct(
                    public float $x = 0.0,
                    protected float $y = 0.0,
                    private float $z = 0.0,
                ) {}
            }',
            '<?php
            class Foo {
                private array $foo;
                private array $bar;
                public function __construct(
                    public float $x = 0.0,
                    protected float $y = 0.0,
                    private float $z = 0.0,
                ) {}
            }',
        ];

        yield 'typed properties' => [
            '<?php
            class Foo {
                private static int | float | null $a;

                private static int | float | null $b;

                private int | float | null $c;

                private int | float | null $d;
            }',
            '<?php
            class Foo {
                private static int | float | null $a;
                private static int | float | null $b;
                private int | float | null $c;
                private int | float | null $d;
            }',
        ];

        yield 'attributes with conditional spacing' => [
            '<?php
class User
{
    private $id;

    #[Assert\String()]
    private $name;
    private $email;
}
',
            '<?php
class User
{

    private $id;
    #[Assert\String()]
    private $name;

    private $email;
}
',
            ['elements' => ['property' => 'only_if_meta']],
        ];

        yield 'mixed attributes and phpdoc with conditional spacing' => [
            '<?php
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
',
            '<?php
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
',
            ['elements' => ['property' => 'only_if_meta']],
        ];

        yield [
            '<?php
class Foo
{
    #[Assert\Email(["message" => "Foo"])]
    private $email;

    private $foo1; #1
    private $foo2; /* @2 */
}',
            '<?php
class Foo
{
    #[Assert\Email(["message" => "Foo"])]

    private $email;

    private $foo1; #1

    private $foo2; /* @2 */
}',
            ['elements' => ['property' => 'none']],
        ];
    }

    /**
     * @dataProvider provideFixClassesWithTraitsCases
     */
    public function testFixClassesWithTraits(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixClassesWithTraitsCases(): iterable
    {
        yield [
            '<?php
class Foo
{
    use SomeTrait1;
    use SomeTrait2;

    public function Bar(){}
}
',
            '<?php
class Foo
{
    use SomeTrait1;

    use SomeTrait2;
    public function Bar(){}
}
',
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

    public function provideFix81Cases(): iterable
    {
        yield [
            '<?php class A {
    public int $a0;

    public readonly int $a1;

    readonly public int $a2;

    readonly int $a3;

    public int $a4;
}',
            '<?php class A {
    public int $a0;
    public readonly int $a1;
    readonly public int $a2;
    readonly int $a3;
    public int $a4;
}',
        ];

        yield [
            '<?php
class Foo
{
    final public const B1 = "1";

    public final const B2 = "2";

    final const B3 = "3";
}
',
            '<?php
class Foo
{
    final public const B1 = "1";
    public final const B2 = "2";


    final const B3 = "3";


}
',
        ];

        yield 'intersection properties' => [
            '<?php
            class Foo {
                private static Bar & Something & Baz $a;

                private static Bar & Something & Baz $b;

                private Bar & Something & Baz $c;

                private Bar & Something & Baz $d;
            }',
            '<?php
            class Foo {
                private static Bar & Something & Baz $a;
                private static Bar & Something & Baz $b;
                private Bar & Something & Baz $c;
                private Bar & Something & Baz $d;
            }',
        ];

        $input = '<?php
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
            ';

        yield [
            '<?php
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
            ',
            $input,
            ['elements' => [
                'const' => 'one',
                'method' => 'one',
                'case' => 'one',
            ]],
        ];

        yield [
            '<?php
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
            ',
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

    public function provideFix82Cases(): iterable
    {
        yield [
            '<?php
            trait Foo {
                const Bar = 1;

                const Baz = 2;
            }',
            '<?php
            trait Foo {
                const Bar = 1;
                const Baz = 2;
            }',
        ];
    }
}
