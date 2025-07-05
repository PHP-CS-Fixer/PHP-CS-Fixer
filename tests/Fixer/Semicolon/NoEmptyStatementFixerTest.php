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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoEmptyStatementFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                }
                ',
            '<?php
                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                };
                ',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
                while($time < $a)
                    ;
                echo "done waiting.";
                $b = \Test;
                ',
        ];

        yield [
            '<?php
                    if($a>1){

                    }
                ',
            '<?php
                    if($a>1){

                    };
                ',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php ',
            '<?php ;',
        ];

        yield [
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
            '<?php
                    for(;;) {
                    }
                ',
            '<?php
                    for(;;) {
                    };
                ',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
                function test($a, $b) {
                }
                ',
            '<?php
                function test($a, $b) {
                };
                ',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
                    namespace A\B\C;
                    use D;
                ',
            '<?php
                    namespace A\B\C;;;;
                    use D;;;;
                ',
        ];

        yield [
            '<?php
                    namespace A\B\C;
                    use D;
                ',
            '<?php
                    namespace A\B\C;
                    use D;;;;
                ',
        ];

        yield [
            '<?php
                    namespace A\B\C;use D;
                ',
            '<?php
                    namespace A\B\C;;use D;
                ',
        ];

        yield [
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
        ];

        yield [
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
        ];

        foreach (['break', 'continue'] as $ops) {
            yield [
                \sprintf('<?php while(true) {%s ;}', $ops),
                \sprintf('<?php while(true) {%s 1;}', $ops),
            ];
        }

        foreach (['1', '1.0', '"foo"', '$foo'] as $noop) {
            yield [
                '<?php echo "foo";  ',
                \sprintf('<?php echo "foo"; %s ;', $noop),
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

        yield [
            '<?php
                    use function Functional\map;
                    $a = new class {
                        public function log($msg)
                        {
                        }
                    };
                    ',
        ];

        yield [
            '<?php
                    use function Functional\map;
                    $a = new class extends A {
                    };
                    ',
        ];

        yield [
            '<?php
                    use function Functional\map;
                    $a = new class implements B {
                    };
                    ',
        ];

        yield [
            '<?php
                    use function Functional\map;
                    $a = new class extends A implements B {
                    };
                    ',
        ];

        yield [
            '<?php
                    $a = new class extends \A implements B\C {
                    };
                    ',
        ];

        yield [
            '<?php {{}}',
            '<?php {{}};',
        ];

        yield [
            '<?php
                    namespace A\B\C {

                    }
                ',
            '<?php
                    namespace A\B\C {

                    };
                ',
        ];

        yield [
            '<?php
                    namespace A{

                    }
                ',
            '<?php
                    namespace A{

                    };
                ',
        ];

        yield [
            '<?php
                    namespace{

                    }
                ',
            '<?php
                    namespace{

                    };
                ',
        ];

        yield [
            '<?php $foo = 2 ; //
                    '.'

                ',
            '<?php $foo = 2 ; //
                    ;

                ',
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
            '<?php $foo = 5;

    ',
            '<?php $foo = 5;;
;
    ;',
        ];

        yield [
            '<?php $foo = 6; ',
            '<?php $foo = 6;; ',
        ];

        yield [
            '<?php for ($i = 7; ; ++$i) {}',
        ];

        yield [
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
        ];
    }

    /**
     * @dataProvider provideWithShortOpenTagCases
     */
    public function testWithShortOpenTag(string $expected, ?string $input = null): void
    {
        if ('1' !== \ini_get('short_open_tag')) {
            self::markTestSkipped('No short tag tests possible.');
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideWithShortOpenTagCases(): iterable
    {
        yield [
            '<? ',
            '<? ;',
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

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php enum Foo{}',
            '<?php enum Foo{};',
        ];
    }

    /**
     * @dataProvider provideFix84Cases
     *
     * @requires PHP 8.4
     */
    public function testFix84(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield 'interface with property hooks' => [
            <<<'PHP'
                <?php interface I
                {
                    public bool $a { get; }
                    public bool $b { get; set; }
                    public bool $c { set; }
                    public bool $d { set; get; }
                    public bool $e {/* hello1 */set/* hello2 */;/* hello3 */get/* hello4 */;/* hello5 */}
                }
                PHP,
        ];

        yield 'abstract class with property hooks' => [
            <<<'PHP'
                <?php abstract class A
                {
                    abstract public bool $a { get; set; }
                    abstract public bool $b { get{} set; }
                    abstract public bool $c { get; set{} }
                }
                PHP,
        ];
    }
}
