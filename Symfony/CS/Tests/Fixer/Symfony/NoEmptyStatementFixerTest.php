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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author SpacePossum
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class NoEmptyStatementFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideNoEmptyStatementsCases
     */
    public function testNoEmptyStatements($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideNoEmptyStatementsCases()
    {
        return array(
            array(
                '<?php
                abstract class TestClass0 extends Test IMPLEMENTS TestInterface, TestInterface2
                {
                }
                ',
                '<?php
                abstract class TestClass0 extends Test IMPLEMENTS TestInterface, TestInterface2
                {
                };
                ',
            ),
            array(
                '<?php
                abstract class TestClass1 EXTENDS Test implements TestInterface
                {
                }
                ',
                '<?php
                abstract class TestClass1 EXTENDS Test implements TestInterface
                {
                };
                ',
            ),
            array(
                '<?php
                CLASS TestClass2 extends Test
                {
                }
                ',
                '<?php
                CLASS TestClass2 extends Test
                {
                };
                ',
            ),
            array(
                '<?php
                class TestClass3 implements TestInterface1
                {
                }
                ',
                '<?php
                class TestClass3 implements TestInterface1
                {
                };
                ',
            ),
            array(
                '<?php
                class TestClass4
                {
                }
                ',
                '<?php
                class TestClass4
                {
                };
                ',
            ),
            array(
                '<?php
                interface TestInterface1
                {
                }
                ',
                '<?php
                interface TestInterface1
                {
                };
                ',
            ),
            array(
                '<?php
                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                }
                ',
                '<?php
                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                };
                ',
            ),
            array(
                '<?php
                namespace Two {
                    $a = 1; {
                    }
                }
                ',
                '<?php
                namespace Two {;;
                    $a = 1; {
                    };
                }
                ',
            ),
            array(
                '<?php
                {
                    '.'
                }
                echo 1;
                ',
                '<?php
                {
                    ;
                };
                echo 1;
                ',
            ),
            array(
                '<?php
                while($time < $a)
                    ;
                echo "done waiting.";
                $b = \Test;
                ',
            ),
            array(
                '<?php
                    if($a>1){

                    }
                ',
                '<?php
                    if($a>1){

                    };
                ',
            ),
            array(
                '<?php
                    if($a>1) {

                    } else {

                    }
                ',
                '<?php
                    if($a>1) {

                    } else {

                    };
                ',
            ),
            array(
                '<?php
                    try{

                    }catch (\Exception $e) {

                    }
                ',
                '<?php
                    try{

                    }catch (\Exception $e) {

                    };
                ',
            ),
            array(
                '<?php ',
                '<?php ;',
            ),
            array(
                '<?php
                    function foo()
                    {
                         // a
                    }
                ',
                '<?php
                    function foo()
                    {
                        ; // a
                    }
                ',
            ),
            array(
                '<?php function foo(){}',
                '<?php function foo(){;;}',
            ),
            array(
                '<?php class Test{}',
                '<?php class Test{};',
            ),
            array(
                '<?php
                    for(;;) {
                    }
                ',
                '<?php
                    for(;;) {
                    };
                ',
            ),
            array(
                '<?php
                    foreach($a as $b) {
                    }
                    foreach($a as $b => $c) {
                    }
                ',
                '<?php
                    foreach($a as $b) {
                    };
                    foreach($a as $b => $c) {
                    };
                ',
            ),
            array(
                '<?php
                    while($a > 1){
                    }
                    do {
                    } while($a>1);
                ',
                '<?php
                    while($a > 1){
                    };
                    do {
                    } while($a>1);
                ',
            ),
            array(
                '<?php
                    switch($a) {
                        default : {echo 1;}
                    }
                ',
                '<?php
                    switch($a) {
                        default : {echo 1;}
                    };
                ',
            ),
            array(
                '<?php
                function test($a, $b) {
                }
                ',
                '<?php
                function test($a, $b) {
                };
                ',
            ),
            array(
                '<?php
                function foo($n)
                {
                    '.'
                    $a = function(){};
                    $b = function() use ($a) {};
                    ++${"a"};
                    switch(fooBar()) {
                        case 5;{
                        }
                    }
                    return $n->{$o};
                }
                ',
                '<?php
                function foo($n)
                {
                    ;
                    $a = function(){};
                    $b = function() use ($a) {};
                    ++${"a"};
                    switch(fooBar()) {
                        case 5;{
                        }
                    };
                    return $n->{$o};
                };
                ',
            ),
            array(
                '<?php
                declare(ticks=1) {
                // entire script here
                }
                declare(ticks=1);
                ',
                '<?php
                declare(ticks=1) {
                // entire script here
                };
                declare(ticks=1);
                ',
            ),
            array(
                '<?php
                    namespace A\B\C;
                    use D;
                ',
                '<?php
                    namespace A\B\C;;;;
                    use D;;;;
                ',
            ),
            array(
                '<?php
                    namespace A\B\C;
                    use D;
                ',
                '<?php
                    namespace A\B\C;
                    use D;;;;
                ',
            ),
            array(
                '<?php
                    namespace A\B\C;use D;
                ',
                '<?php
                    namespace A\B\C;;use D;
                ',
            ),
            array(
                '<?php
                    namespace A\B\C {

                    }
                ',
                '<?php
                    namespace A\B\C {

                    };
                ',
            ),
            array(
                '<?php
                    namespace A{

                    }
                ',
                '<?php
                    namespace A{

                    };
                ',
            ),
            array(
                '<?php
                    namespace{

                    }
                ',
                '<?php
                    namespace{

                    };
                ',
            ),
        );
    }

    /**
     * @dataProvider provide54Cases
     * @requires PHP 5.4
     */
    public function testFix54($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provide54Cases()
    {
        return array(
            array(
                '<?php
                    trait TestTrait
                    {
                    }
                ',
                '<?php
                    trait TestTrait
                    {
                    };
                ',
            ),
        );
    }

    /**
     * @dataProvider provide55Cases
     * @requires PHP 5.5
     */
    public function testFix55($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provide55Cases()
    {
        return array(
            array(
            '<?php
                    try {
                        throw new \Exception("a");
                    } catch (\Exception $e){
                        //
                    } finally {
                    }  '.'
                ',
            '<?php
                    try {
                        throw new \Exception("a");
                    } catch (\Exception $e){
                        //
                    } finally {
                    }  ;
                ',
            ),
        );
    }

    /**
     * @dataProvider providePHP7Cases
     * @requires PHP 7.0
     */
    public function testFixPHP7($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function providePHP7Cases()
    {
        return array(
            array(
                '<?php
                    use function Functional\map;
                    $a = new class {
                        public function log($msg)
                        {
                        }
                    };
                    ',
            ),
            array(
                '<?php
                    use function Functional\map;
                    $a = new class extends A {
                    };
                    ',
            ),
            array(
                '<?php
                    use function Functional\map;
                    $a = new class implements B {
                    };
                    ',
            ),
            array(
                '<?php
                    use function Functional\map;
                    $a = new class extends A implements B {
                    };
                    ',
            ),
            array(
                '<?php
                    $a = new class extends \A implements B\C {
                    };
                    ',
            ),
            array(
                '<?php {{}}',
                '<?php {{}};',
            ),
        );
    }

    /**
     * @dataProvider provideCasesWithShortOpenTag
     */
    public function testCasesWithShortOpenTag($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCasesWithShortOpenTag()
    {
        $cases = array();
        /*
         * short_open_tag setting is ignored by HHVM
         * @see https://github.com/facebook/hhvm/issues/4758
         */
        if (ini_get('short_open_tag') || defined('HHVM_VERSION')) {
            $cases[] =
                array(
                    '<? ',
                    '<? ;',
                );
        }

        // HHVM parses '<?=' as T_ECHO instead of T_OPEN_TAG_WITH_ECHO
        // test the fixer doesn't break anything
        if (ini_get('short_open_tag') && !defined('HHVM_VERSION')) {
            $cases[] =
                array(
                    '<?= ',
                    '<?= ;',
                );
        }

        if (count($cases) < 1) {
            $this->markTestSkipped('No short tag tests possible.');
        }

        return $cases;
    }

    /**
     * @dataProvider provideFixMultipleSemicolonsCases
     */
    public function testFixMultipleSemicolons($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixMultipleSemicolonsCases()
    {
        return array(
            array(
                '<?php $foo = 2 ; //
                    '.'

                ',
                '<?php $foo = 2 ; //
                    ;

                ',
            ),
            array(
                '<?php $foo = 3; /**/ ',
                '<?php $foo = 3; /**/; ;',
            ),
            array(
                '<?php $foo = 1;',
                '<?php $foo = 1;;;',
            ),
            array(
                '<?php $foo = 4; ',
                '<?php $foo = 4;; ;;',
            ),
            array(
                '<?php $foo = 5;

    ',
                '<?php $foo = 5;;
;
    ;',
            ),
            array(
                '<?php $foo = 6; ',
                '<?php $foo = 6;; ',
            ),
            array(
                '<?php for ($i = 7; ; ++$i) {}',
            ),
            array(
                '<?php
                    switch($a){
                        case 8;
                            echo 9;
                    }
                ',
                '<?php
                    switch($a){
                        case 8;;
                            echo 9;
                    }
                ',
            ),
        );
    }
}
