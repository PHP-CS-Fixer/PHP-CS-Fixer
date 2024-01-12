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
            '<?php
                abstract class TestClass0 extends Test IMPLEMENTS TestInterface, TestInterface2
                {
                }'."\n                ",
            '<?php
                abstract class TestClass0 extends Test IMPLEMENTS TestInterface, TestInterface2
                {
                };'."\n                ",
        ];

        yield [
            '<?php
                abstract class TestClass1 EXTENDS Test implements TestInterface
                {
                }'."\n                ",
            '<?php
                abstract class TestClass1 EXTENDS Test implements TestInterface
                {
                };'."\n                ",
        ];

        yield [
            '<?php
                CLASS TestClass2 extends Test
                {
                }'."\n                ",
            '<?php
                CLASS TestClass2 extends Test
                {
                };'."\n                ",
        ];

        yield [
            '<?php
                class TestClass3 implements TestInterface1
                {
                }'."\n                ",
            '<?php
                class TestClass3 implements TestInterface1
                {
                };'."\n                ",
        ];

        yield [
            '<?php
                class TestClass4
                {
                }'."\n                ",
            '<?php
                class TestClass4
                {
                };'."\n                ",
        ];

        yield [
            '<?php
                interface TestInterface1
                {
                }'."\n                ",
            '<?php
                interface TestInterface1
                {
                };'."\n                ",
        ];

        yield [
            '<?php
                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                }'."\n                ",
            '<?php
                interface TestExtendingInterface extends TestInterface2, TestInterface3 {
                };'."\n                ",
        ];

        yield [
            '<?php
                namespace Two {
                    $a = 1; {
                    }
                }'."\n                ",
            '<?php
                namespace Two {;;
                    $a = 1; {
                    };
                }'."\n                ",
        ];

        yield [
            '<?php
                {'."\n                    ".'
                }
                echo 1;'."\n                ",
            '<?php
                {
                    ;
                };
                echo 1;'."\n                ",
        ];

        yield [
            '<?php
                while($time < $a)
                    ;
                echo "done waiting.";
                $b = \Test;'."\n                ",
        ];

        yield [
            '<?php
                    if($a>1){

                    }'."\n                ",
            '<?php
                    if($a>1){

                    };'."\n                ",
        ];

        yield [
            '<?php
                    if($a>1) {

                    } else {

                    }'."\n                ",
            '<?php
                    if($a>1) {

                    } else {

                    };'."\n                ",
        ];

        yield [
            '<?php
                    try{

                    }catch (\Exception $e) {

                    }'."\n                ",
            '<?php
                    try{

                    }catch (\Exception $e) {

                    };'."\n                ",
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
                    }'."\n                ",
            '<?php
                    function foo()
                    {
                        ; // a
                    }'."\n                ",
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
                    }'."\n                ",
            '<?php
                    for(;;) {
                    };'."\n                ",
        ];

        yield [
            '<?php
                    foreach($a as $b) {
                    }
                    foreach($a as $b => $c) {
                    }'."\n                ",
            '<?php
                    foreach($a as $b) {
                    };
                    foreach($a as $b => $c) {
                    };'."\n                ",
        ];

        yield [
            '<?php
                    while($a > 1){
                    }
                    do {
                    } while($a>1);  // 1'."\n                ",
            '<?php
                    while($a > 1){
                    };
                    do {
                    } while($a>1); 1; // 1'."\n                ",
        ];

        yield [
            '<?php
                    switch($a) {
                        default : {echo 1;}
                    }'."\n                ",
            '<?php
                    switch($a) {
                        default : {echo 1;}
                    };'."\n                ",
        ];

        yield [
            '<?php
                function test($a, $b) {
                }'."\n                ",
            '<?php
                function test($a, $b) {
                };'."\n                ",
        ];

        yield [
            '<?php
                function foo($n)
                {'."\n                    ".'
                    $a = function(){};
                    $b = function() use ($a) {};
                    ++${"a"};
                    switch(fooBar()) {
                        case 5;{
                        }
                    }
                    return $n->{$o};
                }'."\n                ",
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
                };'."\n                ",
        ];

        yield [
            '<?php
                declare(ticks=1) {
                // entire script here
                }
                declare(ticks=1);'."\n                ",
            '<?php
                declare(ticks=1) {
                // entire script here
                };
                declare(ticks=1);'."\n                ",
        ];

        yield [
            '<?php
                    namespace A\B\C;
                    use D;'."\n                ",
            '<?php
                    namespace A\B\C;;;;
                    use D;;;;'."\n                ",
        ];

        yield [
            '<?php
                    namespace A\B\C;
                    use D;'."\n                ",
            '<?php
                    namespace A\B\C;
                    use D;;;;'."\n                ",
        ];

        yield [
            '<?php
                    namespace A\B\C;use D;'."\n                ",
            '<?php
                    namespace A\B\C;;use D;'."\n                ",
        ];

        yield [
            '<?php
                    trait TestTrait
                    {
                    }'."\n                ",
            '<?php
                    trait TestTrait
                    {
                    };'."\n                ",
        ];

        yield [
            '<?php
                    try {
                        throw new \Exception("Foo.");
                    } catch (\Exception $e){
                        //
                    } finally {
                    }'."  ".''."\n                ",
            '<?php
                    try {
                        throw new \Exception("Foo.");
                    } catch (\Exception $e){
                        //
                    } finally {
                    }  ;'."\n                ",
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
                while(true) {continue;}'."\n            ",
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
            '<?php
                    use function Functional\map;
                    $a = new class {
                        public function log($msg)
                        {
                        }
                    };'."\n                    ",
        ];

        yield [
            '<?php
                    use function Functional\map;
                    $a = new class extends A {
                    };'."\n                    ",
        ];

        yield [
            '<?php
                    use function Functional\map;
                    $a = new class implements B {
                    };'."\n                    ",
        ];

        yield [
            '<?php
                    use function Functional\map;
                    $a = new class extends A implements B {
                    };'."\n                    ",
        ];

        yield [
            '<?php
                    $a = new class extends \A implements B\C {
                    };'."\n                    ",
        ];

        yield [
            '<?php {{}}',
            '<?php {{}};',
        ];

        yield [
            '<?php
                    namespace A\B\C {

                    }'."\n                ",
            '<?php
                    namespace A\B\C {

                    };'."\n                ",
        ];

        yield [
            '<?php
                    namespace A{

                    }'."\n                ",
            '<?php
                    namespace A{

                    };'."\n                ",
        ];

        yield [
            '<?php
                    namespace{

                    }'."\n                ",
            '<?php
                    namespace{

                    };'."\n                ",
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
            '<?php $foo = 2 ; //'."\n                    ".'
'."\n                ",
            '<?php $foo = 2 ; //
                    ;
'."\n                ",
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
'."\n    ",
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
                    }'."\n                ",
            '<?php
                    switch($a){
                        case 8;;
                            echo 9;
                    }'."\n                ",
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
