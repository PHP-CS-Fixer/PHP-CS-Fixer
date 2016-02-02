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

use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class MethodSeparationFixerTest extends AbstractFixerTestCase
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
        $fixer = $this->getFixer();
        $method = new \ReflectionMethod($fixer, 'findCommentBlockStart');
        $method->setAccessible(true);
        if ($expected !== $result = $method->invoke($fixer, $tokens, $index)) {
            $this->fail(sprintf('Expected index %d (%s) got index %d (%s).', $expected, $tokens[$expected]->toJson(), $result, $tokens[$result]->toJson()));
        }
    }

    public function provideCommentBlockStartDetectionCases()
    {
        return array(
            array(
                4,
                '<?php
                    //ui

                    //j1
                    //k2
                ',
                6,
            ),
            array(
                4,
                '<?php
                    //ui

                    //j1
                    //k2
                ',
                5,
            ),
            array(
                4,
                '<?php
                    /**/

                    //j1
                    //k2
                ',
                6,
            ),
            array(
                4,
                '<?php
                    $a;//j
                    //k
                ',
                6,
            ),
            array(
                2,
                '<?php
                    //a
                ',
                2,
            ),
            array(
                2,
                '<?php
                    //b
                    //c
                ',
                2,
            ),
            array(
                2,
                '<?php
                    //d
                    //e
                ',
                4,
            ),
            array(
                2,
                '<?php
                    /**/
                    //f
                    //g
                    //h
                ',
                8,
            ),
        );
    }

    /**
     * @dataProvider provideFixClassesCases
     */
    public function testFixClasses($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixClassesCases()
    {
        $cases = array();
        $cases[] = array('<?php
class SomeClass1
{
    // This comment
    // is multiline.
    public function echoA()
    {
        echo "a";
    }
}
');
        $cases[] = array(
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
            );
        $cases[] = array(
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
', );
        $cases[] = array(
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
', );
        $cases[] = array('<?php
class SomeClass
{
    // comment
    public function echoA()
    {
        echo "a";
    }
}
');
        $cases[] = array('<?php
class SomeClass
{
    // This comment
    // is multiline.
    public function echoA()
    {
        echo "a";
    }
}
');
        $cases[] = array(
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
        );
        $cases[] = array(
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
        );
        $cases[] = array(
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
        );
        $cases[] = array(
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
function test1(){ echo 1;}
function test2(){ echo 2;}',
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
function test1(){ echo 1;}
function test2(){ echo 2;}',
        );

        $cases[] = array(
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
        );

        // do not touch anonymous functions (since PHP doesn't allow
        // for class attributes being functions :(, we only have to test
        // those used within methods)
        $cases[] = array(
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
        );

        $cases[] = array(
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
        );

        // spaces between methods
        $cases[] = array(
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
}', );
        // don't change correct code
        $cases[] = array(
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
        );

        // do not touch function out of class scope
        $cases[] = array(
            '<?php
function test0() {

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
function test() {

}
function test2() {

}
',
        );

        return $cases;
    }

    /**
     * @requires PHP 5.4
     * @dataProvider provideFixTraitsCases
     */
    public function testFixTraits($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixTraitsCases()
    {
        $cases = array();

        // do not touch well formatted traits
        $cases[] = array(
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
        );

        $cases[] = array(
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
        );

        $cases[] = array(
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
        );

        return $cases;
    }

    /**
     * @dataProvider provideFixInterfaces
     */
    public function testFixInterfaces($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixInterfaces()
    {
        $cases = array();
        $cases[] = array(
            '<?php
interface TestInterface
{
    public function testInterfaceMethod4();

    public function testInterfaceMethod5();

    /**
     * {@link}
     */           '.'
    public function testInterfaceMethod6();

    public function testInterfaceMethod7();

 public function testInterfaceMethod8();
}',
            '<?php
interface TestInterface
{    public function testInterfaceMethod4();
    public function testInterfaceMethod5();


    /**
     * {@link}
     */           '.'
    public function testInterfaceMethod6();


    public function testInterfaceMethod7(); public function testInterfaceMethod8();
}',
        );

        // do not touch well formatted interfaces
        $cases[] = array(
            '<?php
interface TestInterfaceOK
{
    public function testMethod1();

    public function testMethod2();
}',
        );

        // method after trait use
        $cases[] = array(
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
        );

        return $cases;
    }
}
