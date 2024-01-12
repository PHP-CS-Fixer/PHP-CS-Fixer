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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer
 */
final class NoEmptyStatementFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideNoEmptyStatementsCases
     */
    public function testNoEmptyStatements(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideNoEmptyStatementsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                abstract class TestClass0 extends Test IMPLEMENTS TestInterface, TestInterface2
                                {
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                abstract class TestClass0 extends Test IMPLEMENTS TestInterface, TestInterface2
                                {
                                };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                abstract class TestClass1 EXTENDS Test implements TestInterface
                                {
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                abstract class TestClass1 EXTENDS Test implements TestInterface
                                {
                                };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                CLASS TestClass2 extends Test
                                {
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                CLASS TestClass2 extends Test
                                {
                                };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                class TestClass3 implements TestInterface1
                                {
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                class TestClass3 implements TestInterface1
                                {
                                };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                class TestClass4
                                {
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                class TestClass4
                                {
                                };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                interface TestInterface1
                                {
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                interface TestInterface1
                                {
                                };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                                };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace Two {
                                    $a = 1; {
                                    }
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                namespace Two {;;
                                    $a = 1; {
                                    };
                                }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                {
                EOD."\n                    ".<<<'EOD'

                                }
                                echo 1;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                {
                                    ;
                                };
                                echo 1;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                while($time < $a)
                                    ;
                                echo "done waiting.";
                                $b = \Test;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    if($a>1){

                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    if($a>1){

                                    };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    if($a>1) {

                                    } else {

                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    if($a>1) {

                                    } else {

                                    };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    try{

                                    }catch (\Exception $e) {

                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    try{

                                    }catch (\Exception $e) {

                                    };
                EOD."\n                ",
        ];

        yield [
            '<?php ',
            '<?php ;',
        ];

        yield [
            <<<'EOD'
                <?php
                                    function foo()
                                    {
                                         // a
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    function foo()
                                    {
                                        ; // a
                                    }
                EOD."\n                ",
        ];

        yield [
            '<?php function foo(){}',
            '<?php function foo(){;;}',
        ];

        yield [
            '<?php class Test{}',
            '<?php class Test{};',
        ];

        yield [
            <<<'EOD'
                <?php
                                    for(;;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for(;;) {
                                    };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    foreach($a as $b) {
                                    }
                                    foreach($a as $b => $c) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    foreach($a as $b) {
                                    };
                                    foreach($a as $b => $c) {
                                    };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    while($a > 1){
                                    }
                                    do {
                                    } while($a>1);  // 1
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    while($a > 1){
                                    };
                                    do {
                                    } while($a>1); 1; // 1
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    switch($a) {
                                        default : {echo 1;}
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    switch($a) {
                                        default : {echo 1;}
                                    };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                function test($a, $b) {
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function test($a, $b) {
                                };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                function foo($n)
                                {
                EOD."\n                    ".<<<'EOD'

                                    $a = function(){};
                                    $b = function() use ($a) {};
                                    ++${"a"};
                                    switch(fooBar()) {
                                        case 5;{
                                        }
                                    }
                                    return $n->{$o};
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
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
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                declare(ticks=1) {
                                // entire script here
                                }
                                declare(ticks=1);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                declare(ticks=1) {
                                // entire script here
                                };
                                declare(ticks=1);
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    namespace A\B\C;
                                    use D;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    namespace A\B\C;;;;
                                    use D;;;;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    namespace A\B\C;
                                    use D;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    namespace A\B\C;
                                    use D;;;;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    namespace A\B\C;use D;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    namespace A\B\C;;use D;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    trait TestTrait
                                    {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    trait TestTrait
                                    {
                                    };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    try {
                                        throw new \Exception("Foo.");
                                    } catch (\Exception $e){
                                        //
                                    } finally {
                                    }
                EOD.'  '.''."\n                ",
            <<<'EOD'
                <?php
                                    try {
                                        throw new \Exception("Foo.");
                                    } catch (\Exception $e){
                                        //
                                    } finally {
                                    }  ;
                EOD."\n                ",
        ];

        foreach (['break', 'continue'] as $ops) {
            yield [
                sprintf('<?php while(true) {%s ;}', $ops),
                sprintf('<?php while(true) {%s 1;}', $ops),
            ];
        }

        foreach (['1', '1.0', '"foo"', '$foo'] as $noop) {
            yield [
                '<?php echo "foo";  ',
                sprintf('<?php echo "foo"; %s ;', $noop),
            ];
        }

        yield [
            '<?php /* 1 */   /* 2 */  /* 3 */ ',
            '<?php /* 1 */ ;  /* 2 */ 1 /* 3 */ ;',
        ];

        yield [
            <<<'EOD'
                <?php
                                while(true) {while(true) {break 2;}}
                                while(true) {continue;}
                EOD."\n            ",
        ];

        yield [
            '<?php if ($foo1) {} ',
            '<?php if ($foo1) {} 1;',
        ];

        yield [
            '<?php if ($foo2) {}',
            '<?php if ($foo2) {1;}',
        ];
    }

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
            <<<'EOD'
                <?php
                                    use function Functional\map;
                                    $a = new class {
                                        public function log($msg)
                                        {
                                        }
                                    };
                EOD."\n                    ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    use function Functional\map;
                                    $a = new class extends A {
                                    };
                EOD."\n                    ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    use function Functional\map;
                                    $a = new class implements B {
                                    };
                EOD."\n                    ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    use function Functional\map;
                                    $a = new class extends A implements B {
                                    };
                EOD."\n                    ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    $a = new class extends \A implements B\C {
                                    };
                EOD."\n                    ",
        ];

        yield [
            '<?php {{}}',
            '<?php {{}};',
        ];

        yield [
            <<<'EOD'
                <?php
                                    namespace A\B\C {

                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    namespace A\B\C {

                                    };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    namespace A{

                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    namespace A{

                                    };
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    namespace{

                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    namespace{

                                    };
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideCasesWithShortOpenTagCases
     */
    public function testCasesWithShortOpenTag(string $expected, ?string $input = null): void
    {
        if (!\ini_get('short_open_tag')) {
            self::markTestSkipped('No short tag tests possible.');
        }

        $this->doTest($expected, $input);
    }

    public static function provideCasesWithShortOpenTagCases(): iterable
    {
        yield [
            '<? ',
            '<? ;',
        ];
    }

    /**
     * @dataProvider provideFixMultipleSemicolonsCases
     */
    public function testFixMultipleSemicolons(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixMultipleSemicolonsCases(): iterable
    {
        yield [
            '<?php $foo = 2 ; //'."\n                    ".<<<'EOD'


                EOD."\n                ",
            <<<'EOD'
                <?php $foo = 2 ; //
                                    ;

                EOD."\n                ",
        ];

        yield [
            '<?php $foo = 3; /**/ ',
            '<?php $foo = 3; /**/; ;',
        ];

        yield [
            '<?php $foo = 1;',
            '<?php $foo = 1;;;',
        ];

        yield [
            '<?php $foo = 4; ',
            '<?php $foo = 4;; ;;',
        ];

        yield [
            <<<'EOD'
                <?php $foo = 5;

                EOD."\n    ",
            <<<'EOD'
                <?php $foo = 5;;
                ;
                    ;
                EOD,
        ];

        yield [
            '<?php $foo = 6; ',
            '<?php $foo = 6;; ',
        ];

        yield [
            '<?php for ($i = 7; ; ++$i) {}',
        ];

        yield [
            <<<'EOD'
                <?php
                                    switch($a){
                                        case 8;
                                            echo 9;
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    switch($a){
                                        case 8;;
                                            echo 9;
                                    }
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php enum Foo{}',
            '<?php enum Foo{};',
        ];
    }
}
