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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer
 */
final class NoEmptyStatementFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideNoEmptyStatementsCases
     */
    public function testNoEmptyStatements($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideNoEmptyStatementsCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                }
                ',
                '<?php
                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                };
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                while($time < $a)
                    ;
                echo "done waiting.";
                $b = \Test;
                ',
            ],
            [
                '<?php
                    if($a>1){

                    }
                ',
                '<?php
                    if($a>1){

                    };
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php ',
                '<?php ;',
            ],
            [
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
            ],
            [
                '<?php function foo(){}',
                '<?php function foo(){;;}',
            ],
            [
                '<?php class Test{}',
                '<?php class Test{};',
            ],
            [
                '<?php
                    for(;;) {
                    }
                ',
                '<?php
                    for(;;) {
                    };
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                function test($a, $b) {
                }
                ',
                '<?php
                function test($a, $b) {
                };
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                    namespace A\B\C;
                    use D;
                ',
                '<?php
                    namespace A\B\C;;;;
                    use D;;;;
                ',
            ],
            [
                '<?php
                    namespace A\B\C;
                    use D;
                ',
                '<?php
                    namespace A\B\C;
                    use D;;;;
                ',
            ],
            [
                '<?php
                    namespace A\B\C;use D;
                ',
                '<?php
                    namespace A\B\C;;use D;
                ',
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePHP7Cases
     * @requires PHP 7.0
     */
    public function testFixPHP7($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function providePHP7Cases()
    {
        return [
            [
                '<?php
                    use function Functional\map;
                    $a = new class {
                        public function log($msg)
                        {
                        }
                    };
                    ',
            ],
            [
                '<?php
                    use function Functional\map;
                    $a = new class extends A {
                    };
                    ',
            ],
            [
                '<?php
                    use function Functional\map;
                    $a = new class implements B {
                    };
                    ',
            ],
            [
                '<?php
                    use function Functional\map;
                    $a = new class extends A implements B {
                    };
                    ',
            ],
            [
                '<?php
                    $a = new class extends \A implements B\C {
                    };
                    ',
            ],
            [
                '<?php {{}}',
                '<?php {{}};',
            ],
            [
                '<?php
                    namespace A\B\C {

                    }
                ',
                '<?php
                    namespace A\B\C {

                    };
                ',
            ],
            [
                '<?php
                    namespace A{

                    }
                ',
                '<?php
                    namespace A{

                    };
                ',
            ],
            [
                '<?php
                    namespace{

                    }
                ',
                '<?php
                    namespace{

                    };
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCasesWithShortOpenTag
     */
    public function testCasesWithShortOpenTag($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCasesWithShortOpenTag()
    {
        $cases = [];
        /*
         * short_open_tag setting is ignored by HHVM
         * @see https://github.com/facebook/hhvm/issues/4758
         */
        if (ini_get('short_open_tag') || defined('HHVM_VERSION')) {
            $cases[] =
                [
                    '<? ',
                    '<? ;',
                ];
        }

        // HHVM parses '<?=' as T_ECHO instead of T_OPEN_TAG_WITH_ECHO
        // test the fixer doesn't break anything
        if (ini_get('short_open_tag') && !defined('HHVM_VERSION')) {
            $cases[] =
                [
                    '<?= ',
                    '<?= ;',
                ];
        }

        if (count($cases) < 1) {
            $this->markTestSkipped('No short tag tests possible.');
        }

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixMultipleSemicolonsCases
     */
    public function testFixMultipleSemicolons($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixMultipleSemicolonsCases()
    {
        return [
            [
                '<?php $foo = 2 ; //
                    '.'

                ',
                '<?php $foo = 2 ; //
                    ;

                ',
            ],
            [
                '<?php $foo = 3; /**/ ',
                '<?php $foo = 3; /**/; ;',
            ],
            [
                '<?php $foo = 1;',
                '<?php $foo = 1;;;',
            ],
            [
                '<?php $foo = 4; ',
                '<?php $foo = 4;; ;;',
            ],
            [
                '<?php $foo = 5;

    ',
                '<?php $foo = 5;;
;
    ;',
            ],
            [
                '<?php $foo = 6; ',
                '<?php $foo = 6;; ',
            ],
            [
                '<?php for ($i = 7; ; ++$i) {}',
            ],
            [
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
            ],
        ];
    }
}
