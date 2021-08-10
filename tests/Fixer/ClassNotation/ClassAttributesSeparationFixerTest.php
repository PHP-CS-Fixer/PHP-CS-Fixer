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
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
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

        $result = $method->invoke($this->fixer, $tokens, $index);
        static::assertSame(
            $expected,
            $result,
            sprintf('Expected index %d (%s) got index %d (%s).', $expected, $tokens[$expected]->toJson(), $result, $tokens[$result]->toJson())
        );
    }

    public function provideCommentBlockStartDetectionCases()
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

    public function provideFixClassesCases()
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

    public function provideFixTraitsCases()
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

    public function provideFixInterfaceCases()
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

    public function provideMessyWhitespacesCases()
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
     * @dataProvider provideConfigCases
     */
    public function testWithConfig(string $expected, string $input, array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideConfigCases()
    {
        return [
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
                ['elements' => ['property' => ClassAttributesSeparationFixer::SPACING_ONE]],
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
                ['elements' => ['property' => ClassAttributesSeparationFixer::SPACING_NONE]],
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
                ['elements' => ['const' => ClassAttributesSeparationFixer::SPACING_ONE]],
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
                ['elements' => ['const' => ClassAttributesSeparationFixer::SPACING_NONE]],
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
                ['elements' => ['method' => ClassAttributesSeparationFixer::SPACING_ONE]],
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
                ['elements' => ['method' => ClassAttributesSeparationFixer::SPACING_NONE]],
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
                ['elements' => ['property' => ClassAttributesSeparationFixer::SPACING_NONE, 'method' => ClassAttributesSeparationFixer::SPACING_ONE]],
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
                ['elements' => ['const' => ClassAttributesSeparationFixer::SPACING_NONE, 'method' => ClassAttributesSeparationFixer::SPACING_ONE]],
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
                ['elements' => ['const' => ClassAttributesSeparationFixer::SPACING_NONE, 'method' => ClassAttributesSeparationFixer::SPACING_ONE]],
            ],
            [
                '<?php
                    class A
                    {
                        use FooTrait;
                        use BarTrait;

                        const FOO = 1;

                        const BAR = 2;

                        public function f1() {}

                        public function f2() {}
                     }
                ',
                '<?php
                    class A
                    {
                        use FooTrait;
                        use BarTrait;
                        const FOO = 1;
                        const BAR = 2;
                        public function f1() {}
                        public function f2() {}
                     }
                ',
                ['elements' => ['const' => 'one', 'method' => 'one', 'trait_import' => 'none']],
            ],
            [
                '<?php
                    class A
                    {
                        public static function foo() {}

                        use ATrait;

                        private $a = 1;

                        public function f1() {}
                        public function f2() {}
                    }
                ',
                '<?php
                    class A
                    {
                        public static function foo() {}
                        use ATrait;
                        private $a = 1;
                        public function f1() {}
                        public function f2() {}
                    }
                ',
                ['elements' => ['method' => 'none', 'trait_import' => 'one', 'property' => 'one']],
            ],
            [
                '<?php
                    class A
                    {
                        public static function foo() {}

                        private $a = 1;

                        public function f1() {}
                        public function f2() {}
                    }
                ',
                '<?php
                    class A
                    {
                        public static function foo() {}
                        private $a = 1;
                        public function f1() {}
                        public function f2() {}
                    }
                ',
                ['elements' => ['method' => 'none', 'property' => 'one']],
            ],
        ];
    }

    /**
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        $to = $from = '<?php ';

        for ($i = 0; $i < 15; ++$i) {
            $from .= sprintf('class A%d{public function GA%d(){return new class {public function B6B%d(){}};}public function otherFunction%d(){}}', $i, $i, $i, $i);
            $to .= sprintf("class A%d{\npublic function GA%d(){return new class {\npublic function B6B%d(){}\n};}\n\npublic function otherFunction%d(){}\n}", $i, $i, $i, $i);
        }

        return [
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
     * @dataProvider provideFix71Cases
     * @requires PHP 7.1
     */
    public function testFix71(string $expected, string $input): void
    {
        $this->fixer->configure([
            'elements' => ['method' => ClassAttributesSeparationFixer::SPACING_ONE, 'const' => ClassAttributesSeparationFixer::SPACING_ONE],
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFix71Cases()
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
     * @dataProvider provideFix74Cases
     * @requires PHP 7.4
     */
    public function testFix74(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideFix74Cases()
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
            null,
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
            null,
        ];

        yield [
            '<?php
            class A
            {
                private array $foo;
                private array $bar;

                public function qux() {}
                public function baz() {}
            }',
            '<?php
            class A
            {
                private array $foo;

                private array $bar;

                public function qux() {}

                public function baz() {}
            }',
            ['elements' => ['property' => 'none', 'method' => 'none']],
        ];
    }

    /**
     * @dataProvider provideFixPhp80Cases
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPhp80Cases()
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

        yield 'attributes not blocks' => [
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

        yield [
            '<?php
            class Foo
            {
                use Bar;

                use Baz { bazzy as private; }

                use Char {
                    ping as public charPing;
                    pong as private charPong;
                }

                use Qux;

                use Bon;

                public function isFoo() {}
            }
            ',
            '<?php
            class Foo
            {
                use Bar;
                use Baz { bazzy as private; }
                use Char {
                    ping as public charPing;
                    pong as private charPong;
                }
                use Qux;
                use Bon;
                public function isFoo() {}
            }
            ',
        ];
    }
}
