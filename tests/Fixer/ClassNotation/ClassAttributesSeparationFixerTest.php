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
     * @param int    $expected
     * @param string $code
     * @param int    $index
     *
     * @dataProvider provideCommentBlockStartDetectionCases
     */
    public function testCommentBlockStartDetection($expected, $code, $index)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($code);
        $method = new \ReflectionMethod($this->fixer, 'findCommentBlockStart');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $index);
        $this->assertSame(
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixClassesCases
     */
    public function testFixClasses($expected, $input = null)
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
public function B(); // allowed comment

                public function C(); // allowed comment
            }',
            '<?php interface A {public function B(); // allowed comment
                public function C(); // allowed comment
            }',
        ];

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     *
     * @dataProvider provideFixTraitsCases
     */
    public function testFixTraits($expected, $input = null)
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixInterfaceCases
     */
    public function testFixInterface($expected, $input = null)
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
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
     * @param string      $expected
     * @param null|string $input
     * @param array       $config
     *
     * @dataProvider provideConfigCases
     */
    public function testWithConfig($expected, $input = null, array $config = [])
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

                    function A(){}}
                ',
                '<?php
                    class A
                    {
                        private $a = null;
                        public $b = 1;



                    function A(){}}
                ',
                ['elements' => ['property']],
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
                        const B = 2;}
                ',
                ['elements' => ['const']],
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        $to = $from = '<?php ';

        for ($i = 0; $i < 15; ++$i) {
            $from .= sprintf('class A%d{public function AA%d(){return new class {public function BB%d(){}};}public function otherFunction%d(){}}', $i, $i, $i, $i);
            $to .= sprintf("class A%d{\npublic function AA%d(){return new class {\npublic function BB%d(){}\n};}\n\npublic function otherFunction%d(){}\n}", $i, $i, $i, $i);
        }

        return [
            [$to, $from],
            [
                '<?php $a = new class {
                public function A(){}

                public function B(){}

                private function C(){}
                };',
                '<?php $a = new class {
                public function A(){}
                public function B(){}
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix71Cases
     * @requires PHP 7.1
     */
    public function testFix71($expected, $input = null)
    {
        $this->fixer->configure([
            'elements' => ['method', 'const'],
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFix71Cases()
    {
        return [
            [
                '<?php
                class Foo {
    public abstract function A(){}

    /**  */
    public const BAR = 123;

    /**  */
    private const BAZ = "a";
                }',
                '<?php
                class Foo {



    public abstract function A(){}


    /**  */
    public const BAR = 123;
    /**  */
    private const BAZ = "a";


                }',
            ],
        ];
    }
}
