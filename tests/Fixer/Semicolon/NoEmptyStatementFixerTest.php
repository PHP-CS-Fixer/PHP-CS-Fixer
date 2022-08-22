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

    public function provideNoEmptyStatementsCases(): iterable
    {
        yield from [
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
                    } while($a>1);  // 1
                ',
                '<?php
                    while($a > 1){
                    };
                    do {
                    } while($a>1); 1; // 1
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
                        throw new \Exception("Foo.");
                    } catch (\Exception $e){
                        //
                    } finally {
                    }  '.'
                ',
                '<?php
                    try {
                        throw new \Exception("Foo.");
                    } catch (\Exception $e){
                        //
                    } finally {
                    }  ;
                ',
            ],
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
            '<?php
                while(true) {while(true) {break 2;}}
                while(true) {continue;}
            ',
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

    public function provideFixCases(): array
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
     * @dataProvider provideCasesWithShortOpenTagCases
     */
    public function testCasesWithShortOpenTag(string $expected, ?string $input = null): void
    {
        if (!\ini_get('short_open_tag')) {
            static::markTestSkipped('No short tag tests possible.');
        }

        $this->doTest($expected, $input);
    }

    public function provideCasesWithShortOpenTagCases(): array
    {
        return [
            [
                '<? ',
                '<? ;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixMultipleSemicolonsCases
     */
    public function testFixMultipleSemicolons(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixMultipleSemicolonsCases(): array
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

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield [
            '<?php enum Foo{}',
            '<?php enum Foo{};',
        ];
    }
}
