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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer
 */
final class YodaStyleFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $extraConfig
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $extraConfig = []): void
    {
        $this->fixer->configure(['equal' => true, 'identical' => true] + $extraConfig);
        $this->doTest($expected, $input);
    }

    /**
     * Test with the inverse config.
     *
     * @param array<string, mixed> $extraConfig
     *
     * @dataProvider provideFixCases
     */
    public function testFixInverse(string $expected, ?string $input = null, array $extraConfig = []): void
    {
        $this->fixer->configure(['equal' => false, 'identical' => false] + $extraConfig);

        if (null === $input) {
            $this->doTest($expected);
        } else {
            $this->doTest($input, $expected);
        }
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $a = 1 + ($b + $c) === true ? 1 : 2;',
            null,
            ['always_move_variable' => true],
        ];

        yield [
            '<?php $a = true === ($b + $c) ? 1 : 2;',
            '<?php $a = ($b + $c) === true ? 1 : 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php
if ((1 === $a) === 1) {
    return;
}',
            '<?php
if (($a === 1) === 1) {
    return;
}',
            ['always_move_variable' => false],
        ];

        yield [
            '<?php
if (true === (1 !== $foo[0])) {
    return;
}',
            '<?php
if (($foo[0] !== 1) === true) {
    return;
}',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return 1 !== $a [$b];',
            '<?php return $a [$b] !== 1;',
        ];

        yield [
            '<?= 1 === $a ? 5 : 7;',
            '<?= $a === 1 ? 5 : 7;',
        ];

        yield [
            '<?php print 1 === 1343;',
        ];

        yield [
            '<?php
                echo 3 === $a ? 2 : 4;
                ',
            '<?php
                echo $a === 3 ? 2 : 4;
                ',
        ];

        yield [
            '<?php 1 === foo($a) ? 1 : 2;',
            '<?php foo($a) === 1 ? 1 : 2;',
        ];

        yield [
            '<?php 1 === $a::$a ? 1 : 2;',
            '<?php $a::$a === 1 ? 1 : 2;',
        ];

        yield [
            '<?php 1 === (bool) $a ? 8 : 7;',
            '<?php (bool) $a === 1 ? 8 : 7;',
        ];

        yield [
            '<?php 1 === new $a ? 1 : 2;',
            '<?php new $a === 1 ? 1 : 2;',
        ];

        yield [
            '<?php 1 === "a".$a ? 5 : 6;',
            '<?php "a".$a === 1 ? 5 : 6;',
        ];

        yield [
            '<?php 1 === __DIR__.$a ? 5 : 6;',
            '<?php __DIR__.$a === 1 ? 5 : 6;',
        ];

        yield [
            '<?php 1 === $a.$b ? 5 : 6;',
            '<?php $a.$b === 1 ? 5 : 6;',
        ];

        yield [
            '<?php echo 1 === (object) $a ? 8 : 7;',
            '<?php echo (object) $a === 1 ? 8 : 7;',
        ];

        yield [
            '<?php echo 1 === (int) $a ? 8 : 7;',
            '<?php echo (int) $a === 1 ? 8 : 7;',
        ];

        yield [
            '<?php echo 1 === (float) $a ? 8 : 7;',
            '<?php echo (float) $a === 1 ? 8 : 7;',
        ];

        yield [
            '<?php echo 1 === (string) $a ? 8 : 7;',
            '<?php echo (string) $a === 1 ? 8 : 7;',
        ];

        yield [
            '<?php echo 1 === (array) $a ? 8 : 7;',
            '<?php echo (array) $a === 1 ? 8 : 7;',
        ];

        yield [
            '<?php echo 1 === (bool) $a ? 8 : 7;',
            '<?php echo (bool) $a === 1 ? 8 : 7;',
        ];

        yield [
            '<?php
if ($a = true === $obj instanceof A) {
    echo \'A\';
}',
            '<?php
if ($a = $obj instanceof A === true) {
    echo \'A\';
}',
        ];

        yield [
            '<?php echo 1 === !!$a ? 8 : 7;',
            '<?php echo !!$a === 1 ? 8 : 7;',
        ];

        yield [
            '<?php 1 === new $a ? 1 : 2;',
            '<?php new $a === 1 ? 1 : 2;',
        ];

        yield [
            '<?php $a = 1 === new b ? 1 : 2;',
            '<?php $a = new b === 1 ? 1 : 2;',
        ];

        yield [
            '<?php $a = 1 === empty($a) ? 1 : 2;',
            '<?php $a = empty($a) === 1 ? 1 : 2;',
        ];

        yield [
            '<?php $b = 1 === clone $a ? 5 : 9;',
            '<?php $b = clone $a === 1 ? 5 : 9;',
        ];

        yield [
            '<?php while(1 === $a ? 1 : 2){};',
            '<?php while($a === 1 ? 1 : 2){};',
        ];

        yield [
            '<?php switch(1 === $a){
                    case true: echo 1;
                };',
            '<?php switch($a === 1){
                    case true: echo 1;
                };',
        ];

        yield [
            '<?php echo 1 === $a ? 1 : 2;',
            '<?php echo $a === 1 ? 1 : 2;',
        ];

        // Don't fix cases.
        yield ['<?php $a = 1 === 1;'];

        yield ['<?php $b = $b === $c;'];

        yield ['<?php $c = $$b === $$c;'];

        yield ['<?php $d = count($this->array[$var]) === $a;'];

        yield ['<?php $e = $a === count($this->array[$var]);'];

        yield ['<?php $f = ($a123 & self::MY_BITMASK) === $a;'];

        yield ['<?php $g = $a === ($a456 & self::MY_BITMASK);'];

        yield ['<?php $h = $this->getStuff() === $myVariable;'];

        yield ['<?php $i = $myVariable === $this->getStuff();'];

        yield ['<?php $j = 2 * $myVar % 3 === $a;'];

        yield ['<?php return $k === 2 * $myVar % 3;'];

        yield ['<?php $l = $c > 2;'];

        yield ['<?php return $this->myObject1->{$index}+$b === "";'];

        yield ['<?php return $m[2]+1 == 2;'];

        yield ['<?php return $foo === $bar[$baz][1];'];

        yield ['<?php $a = $b[$key]["1"] === $c["2"];'];

        yield ['<?php return $foo->$a === $foo->$b->$c;'];

        yield ['<?php return $x === 2 - 1;'];

        yield ['<?php return $x === 2-1;'];

        // https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/pull/693
        yield ['<?php return array(2) == $o;'];

        yield ['<?php return $p == array(2);'];

        yield ['<?php return $p == array("2");'];

        yield ['<?php return $p == array(TWO);'];

        yield ['<?php return $p == array(array());'];

        yield ['<?php return $p == [[]];'];

        yield ['<?php return array($q) == $a;'];

        yield ['<?php return $r == array($a);'];

        yield ['<?php $s = ((array(2))) == $a;'];

        yield ['<?php $t = $a == ((array(2)));'];

        yield ['<?php list($a) = $c === array(1) ? $b : $d;'];

        yield ['<?php $b = 7 === list($a) = [7];'];

        yield ['<?php $a = function(){} === array(0);'];

        yield ['<?php $z = $n == list($a) = $b;'];

        yield ['<?php return $n == list($a) = $b;'];

        // Fix cases.
        yield 'Array destruct by ternary.' => [
            '<?php list($a) = 11 === $c ? $b : $d;',
            '<?php list($a) = $c === 11 ? $b : $d;',
        ];

        yield 'Less spacing.' => [
            '<?php $z=2==$a;$b=$c>1&&$c<=10;',
            '<?php $z=$a==2;$b=$c>1&&$c<=10;',
        ];

        yield 'Comments.' => [
            '<?php $z = /**/ /**/2/**/ /**/
                 # aa
                 /**/==/**/$a/***/;',
            '<?php $z = /**/ /**/$a/**/ /**/
                 # aa
                 /**/==/**/2/***/;',
        ];

        yield [
            '<?php return 2 == ($a)?>',
        ];

        yield [
            '<?php return ($a) == 2?>',
        ];

        yield [
            '<?php return 2 == ($a)?>',
            '<?php return ($a) == 2?>',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php $a = ($c === ((null === $b)));',
            '<?php $a = ($c === (($b === null)));',
        ];

        yield [
            '<?php return null == $a[2];',
            '<?php return $a[2] == null;',
        ];

        yield [
            '<?php return "" === $this->myArray[$index];',
            '<?php return $this->myArray[$index] === "";',
        ];

        yield [
            '<?php return "" === $this->myArray[$index]->/*1*//*2*//*3*/a;',
            '<?php return $this->myArray[$index]->/*1*//*2*//*3*/a === "";',
        ];

        yield [
            '<?php return "" === $this->myArray[$index]->a;',
            '<?php return $this->myArray[$index]->a === "";',
        ];

        yield [
            '<?php return "" === $this->myObject2->  {$index};',
            '<?php return $this->myObject2->  {$index} === "";',
        ];

        yield [
            '<?php return "" === $this->myObject3->{$index}->a;',
            '<?php return $this->myObject3->{$index}->a === "";',
        ];

        yield [
            '<?php return "" === $this->myObject4->{$index}->{$index}->a;',
            '<?php return $this->myObject4->{$index}->{$index}->a === "";',
        ];

        yield [
            '<?php return "" === $this->myObject4->$index->a;',
            '<?php return $this->myObject4->$index->a === "";',
        ];

        yield [
            '<?php return self::MY_CONST === self::$myVariable;',
            '<?php return self::$myVariable === self::MY_CONST;',
        ];

        yield [
            '<?php return \A\B\C::MY_CONST === \A\B\C::$myVariable;',
            '<?php return \A\B\C::$myVariable === \A\B\C::MY_CONST;',
        ];

        yield [
            '<?php $a = 1 == $$a?>',
            '<?php $a = $$a == 1?>',
        ];

        yield 'Nested case' => [
            '<?php return null === $a[0 === $b ? $c : $d];',
            '<?php return $a[$b === 0 ? $c : $d] === null;',
        ];

        yield [
            '<?php return null === $this->{null === $a ? "a" : "b"};',
            '<?php return $this->{$a === null ? "a" : "b"} === null;',
        ];

        yield 'Complex code sample.' => [
            '<?php
if ($a == $b) {
    return null === $b ? (null === $a ? 0 : 0 === $a->b) : 0 === $b->a;
} else {
    if ($c === (null === $b)) {
        return false === $d;
    }
}',
            '<?php
if ($a == $b) {
    return $b === null ? ($a === null ? 0 : $a->b === 0) : $b->a === 0;
} else {
    if ($c === ($b === null)) {
        return $d === false;
    }
}',
        ];

        yield [
            '<?php $b = list($a) = 7 === [7];', // makes no sense, but valid PHP syntax
            '<?php $b = list($a) = [7] === 7;',
        ];

        yield [
            '<?php $a = 1 === function(){};',
            '<?php $a = function(){} === 1;',
        ];

        yield [
            '<?php
$z#1
#2
=
#3
1#4
#5
===#6
#7
$a#8
#9
;#10',
            '<?php
$z#1
#2
=
#3
$a#4
#5
===#6
#7
1#8
#9
;#10',
        ];

        yield [
            '<?php $i = 2 === $this/*a*//*b*//*c*//*d*//*e*//*f*/->getStuff();',
            '<?php $i = $this/*a*//*b*//*c*//*d*//*e*//*f*/->getStuff() === 2;',
        ];

        yield [
            '<?php return "" === $this->myObject5->{$index}->/*1*//*2*/b;',
            '<?php return $this->myObject5->{$index}->/*1*//*2*/b === "";',
        ];

        yield [
            '<?php
                function hello() {}
                1 === $a ? b() : c();
                ',
            '<?php
                function hello() {}
                $a === 1 ? b() : c();
                ',
        ];

        yield [
            '<?php
                class A{}
                1 === $a ? b() : c();
                ',
            '<?php
                class A{}
                $a === 1 ? b() : c();
                ',
        ];

        yield [
            '<?php
                function foo() {
                    foreach ($arr as $key => $value) {
                        false !== uniqid() ? 1 : 2;
                    }
                    false !== uniqid() ? 1 : 2;
                }',
            '<?php
                function foo() {
                    foreach ($arr as $key => $value) {
                        uniqid() !== false ? 1 : 2;
                    }
                    uniqid() !== false ? 1 : 2;
                }',
        ];

        yield [
            '<?php false === $a = array();',
        ];

        yield [
            '<?php $e = count($this->array[$var]) === $a;',
            '<?php $e = $a === count($this->array[$var]);',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php $i = $this->getStuff() === $myVariable;',
            '<?php $i = $myVariable === $this->getStuff();',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php $e = count($this->array[$var]) === $a;',
            '<?php $e = $a === count($this->array[$var]);',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php $g = ($a789 & self::MY_BITMASK) === $a;',
            null,
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar + 2 === $k;',
            '<?php return $k === $myVar + 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar . $b === $k;',
            '<?php return $k === $myVar . $b;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar - 2 === $k;',
            '<?php return $k === $myVar - 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar * 2 === $k;',
            '<?php return $k === $myVar * 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar / 2 === $k;',
            '<?php return $k === $myVar / 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar % 2 === $k;',
            '<?php return $k === $myVar % 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar ** 2 === $k;',
            '<?php return $k === $myVar ** 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar < 2 === $k;',
            '<?php return $k === $myVar < 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar > 2 === $k;',
            '<?php return $k === $myVar > 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar <= 2 === $k;',
            '<?php return $k === $myVar <= 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar >= 2 === $k;',
            '<?php return $k === $myVar >= 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar . 2 === $k;',
            '<?php return $k === $myVar . 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar << 2 === $k;',
            '<?php return $k === $myVar << 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar >> 2 === $k;',
            '<?php return $k === $myVar >> 2;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return !$myVar === $k;',
            '<?php return $k === !$myVar;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return $myVar instanceof Foo === $k;',
            '<?php return $k === $myVar instanceof Foo;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return (bool) $myVar === $k;',
            '<?php return $k === (bool) $myVar;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return (int) $myVar === $k;',
            '<?php return $k === (int) $myVar;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return (float) $myVar === $k;',
            '<?php return $k === (float) $myVar;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return (string) $myVar === $k;',
            '<?php return $k === (string) $myVar;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return (array) $myVar === $k;',
            '<?php return $k === (array) $myVar;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php return (object) $myVar === $k;',
            '<?php return $k === (object) $myVar;',
            ['always_move_variable' => true],
        ];

        yield [
            '<?php $a = null === foo();',
            '<?php $a = foo() === null;',
        ];

        yield [
            '<?php $a = \'foo\' === foo();',
            '<?php $a = foo() === \'foo\';',
        ];

        yield [
            '<?php $a = "foo" === foo();',
            '<?php $a = foo() === "foo";',
        ];

        yield [
            '<?php $a = 1 === foo();',
            '<?php $a = foo() === 1;',
        ];

        yield [
            '<?php $a = 1.2 === foo();',
            '<?php $a = foo() === 1.2;',
        ];

        yield [
            '<?php $a = true === foo();',
            '<?php $a = foo() === true;',
        ];

        yield [
            '<?php $a = false === foo();',
            '<?php $a = foo() === false;',
        ];

        yield [
            '<?php $a = -1 === reset($foo);',
            '<?php $a = reset($foo) === -1;',
        ];

        yield [
            '<?php $a = - 1 === reset($foo);',
            '<?php $a = reset($foo) === - 1;',
        ];

        yield [
            '<?php $a = -/* bar */1 === reset($foo);',
            '<?php $a = reset($foo) === -/* bar */1;',
        ];

        yield [
            '<?php $a %= 4 === $b ? 2 : 3;',
            '<?php $a %= $b === 4 ? 2 : 3;',
        ];

        yield [
            '<?php return array() === $array;',
            '<?php return $array === array();',
        ];

        yield [
            '<?php return [] === $array;',
            '<?php return $array === [];',
        ];

        yield [
            '<?php return array(/* foo */) === $array;',
            '<?php return $array === array(/* foo */);',
        ];

        yield [
            '<?php return [
                    // 1
                ] === $array;',
            '<?php return $array === [
                    // 1
                ];',
        ];

        yield [
            '<?php $a = $b = null === $c;',
            '<?php $a = $b = $c === null;',
        ];

        $template = '<?php $a = ($b + $c) %s 1 === true ? 1 : 2;';
        $operators = ['||', '&&'];

        foreach ($operators as $operator) {
            yield [
                sprintf($template, $operator),
                null,
                ['always_move_variable' => true],
            ];
        }

        $assignmentOperators = ['=', '**=', '*=', '|=', '+=', '-=', '^=', '<<=', '>>=', '&=', '.=', '/=', '%=', '??='];
        $logicalOperators = ['xor', 'or', 'and', '||', '&&', '??'];

        foreach ([...$assignmentOperators, ...$logicalOperators] as $operator) {
            yield [
                sprintf('<?php $a %s 4 === $b ? 2 : 3;', $operator),
                sprintf('<?php $a %s $b === 4 ? 2 : 3;', $operator),
            ];
        }

        foreach ($assignmentOperators as $operator) {
            yield [
                sprintf('<?php 1 === $x %s 2;', $operator),
            ];
        }

        yield ['<?php $a = $b + 1 <=> $d;'];

        yield [
            '<?php $a = new class(10) extends SomeClass implements SomeInterface {} === $a;/**/',
        ];

        yield [
            '<?php $a = $b ?? 1 ?? 2 == $d;',
            '<?php $a = $b ?? 1 ?? $d == 2;',
        ];

        yield [
            '<?php $a = 1 === new class(10) extends SomeClass implements SomeInterface {};/**/',
            '<?php $a = new class(10) extends SomeClass implements SomeInterface {} === 1;/**/',
        ];

        yield [
            '<?php
function a() {
    for ($i = 1; $i <= 3; $i++) {
        echo yield 1 === $i ? 1 : 2;
    }
}
',
            '<?php
function a() {
    for ($i = 1; $i <= 3; $i++) {
        echo yield $i === 1 ? 1 : 2;
    }
}
',
        ];

        yield [
            '<?php function test() {return yield 1 !== $a [$b];};',
            '<?php function test() {return yield $a [$b] !== 1;};',
        ];

        yield [
            '<?php function test() {return yield 1 === $a;};',
            '<?php function test() {return yield $a === 1;};',
        ];

        yield [
            '<?php
$a = 1;
switch ($a) {
    case 1 === $a:
        echo 123;
        break;
}
',
            '<?php
$a = 1;
switch ($a) {
    case $a === 1:
        echo 123;
        break;
}
',
        ];

        yield 'require' => [
            '<?php require 1 === $var ? "A.php" : "B.php";',
            '<?php require $var === 1 ? "A.php" : "B.php";',
        ];

        yield 'require_once' => [
            '<?php require_once 1 === $var ? "A.php" : "B.php";',
            '<?php require_once $var === 1 ? "A.php" : "B.php";',
        ];

        yield 'include' => [
            '<?php include 1 === $var ? "A.php" : "B.php";',
            '<?php include $var === 1 ? "A.php" : "B.php";',
        ];

        yield 'include_once' => [
            '<?php include_once 1 === $var ? "A.php" : "B.php";',
            '<?php include_once $var === 1 ? "A.php" : "B.php";',
        ];

        yield 'yield from' => [
            '<?php function test() {return yield from 1 === $a ? $c : $d;};',
            '<?php function test() {return yield from $a === 1 ? $c : $d;};',
        ];
    }

    /**
     * @dataProvider provideLessGreaterCases
     */
    public function testFixLessGreater(string $expected, string $input): void
    {
        $this->fixer->configure(['less_and_greater' => true]);
        $this->doTest($expected, $input);
    }

    /**
     * Test with the inverse config.
     *
     * @dataProvider provideLessGreaterCases
     */
    public function testFixLessGreaterInverse(string $expected, string $input): void
    {
        $this->fixer->configure(['less_and_greater' => false]);
        $this->doTest($input, $expected);
    }

    public static function provideLessGreaterCases(): iterable
    {
        yield [
            '<?php $a = 3 <= $b;',
            '<?php $a = $b >= 3;',
        ];

        yield [
            '<?php $a = 3 > $b;',
            '<?php $a = $b < 3;',
        ];

        yield [
            '<?php $a = (3 > $b) || $d;',
            '<?php $a = ($b < 3) || $d;',
        ];
    }

    public function testComplexConfiguration(): void
    {
        $this->fixer->configure([
            'equal' => null,
            'identical' => true,
            'less_and_greater' => false,
        ]);

        $this->doTest(
            '<?php
                $a = 1 === $b;
                $b = $c != 1;
                $c = $c > 3;
            ',
            '<?php
                $a = $b === 1;
                $b = $c != 1;
                $c = $c > 3;
            '
        );
    }

    /**
     * @param array<mixed> $config
     *
     * @dataProvider provideInvalidConfigCases
     */
    public function testInvalidConfig(array $config, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches("#^\\[{$this->fixer->getName()}\\] {$expectedMessage}$#");

        $this->fixer->configure($config);
    }

    public static function provideInvalidConfigCases(): iterable
    {
        yield [['equal' => 2], 'Invalid configuration: The option "equal" with value 2 is expected to be of type "bool" or "null", but is of type "(int|integer)"\.'];

        yield [['_invalid_' => true], 'Invalid configuration: The option "_invalid_" does not exist\. Defined options are: "always_move_variable", "equal", "identical", "less_and_greater"\.'];
    }

    public function testDefinition(): void
    {
        self::assertInstanceOf(\PhpCsFixer\FixerDefinition\FixerDefinitionInterface::class, $this->fixer->getDefinition());
    }

    /**
     * @dataProvider providePHP71Cases
     */
    public function testPHP71(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['equal' => true, 'identical' => true]);
        $this->doTest($expected, $input);
    }

    /**
     * Test with the inverse config.
     *
     * @dataProvider providePHP71Cases
     */
    public function testPHP71Inverse(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['equal' => false, 'identical' => false]);

        if (null === $input) {
            $this->doTest($expected);
        } else {
            $this->doTest($input, $expected);
        }
    }

    public static function providePHP71Cases(): iterable
    {
        // no fix cases
        yield ['<?php list("a" => $a, "b" => $b, "c" => $c) = $c === array(1) ? $b : $d;'];

        yield ['<?php list(list("x" => $x1, "y" => $y1), list("x" => $x2, "y" => $y2)) = $points;'];

        yield ['<?php list("first" => list($x1, $y1), "second" => list($x2, $y2)) = $points;'];

        yield ['<?php [$a, $b, $c] = [1, 2, 3];'];

        yield ['<?php ["a" => $a, "b" => $b, "c" => $c] = $a[0];'];

        yield ['<?php list("a" => $a, "b" => $b, "c" => $c) = $c === array(1) ? $b : $d;'];

        yield ['<?php $b = 7 === [$a] = [7];']; // makes no sense, but valid PHP syntax

        yield ['<?php $b = 7 === [$a] = [7];'];

        yield ['<?php [$a] = $c === array(1) ? $b : $d;'];

        yield ['<?php $b = 7 === [$a] = [7];'];

        yield ['<?php $z = $n == [$a] = $b;'];

        yield ['<?php return $n == [$a] = $b;'];

        // fix cases
        yield [
            '<?php list("a" => $a, "b" => $b, "c" => $c) = 1 === $c ? $b : $d;',
            '<?php list("a" => $a, "b" => $b, "c" => $c) = $c === 1 ? $b : $d;',
        ];

        yield [
            '<?php list("a" => $a, "b" => $b, "c" => $c) = A::B === $c ? $b : $d;',
            '<?php list("a" => $a, "b" => $b, "c" => $c) = $c === A::B ? $b : $d;',
        ];

        yield [
            '<?php list( (2 === $c ? "a" : "b") => $b) = ["a" => 7 === $c ? 5 : 1, "b" => 7];',
            '<?php list( ($c === 2 ? "a" : "b") => $b) = ["a" => $c === 7 ? 5 : 1, "b" => 7];',
        ];

        yield [
            '<?php [ (ABC::A === $c ? "a" : "b") => $b] = ["a" => 7 === $c ? 5 : 1, "b" => 7];',
            '<?php [ ($c === ABC::A ? "a" : "b") => $b] = ["a" => $c === 7 ? 5 : 1, "b" => 7];',
        ];

        yield 'Array destruct by ternary.' => [
            '<?php [$a] = 11 === $c ? $b : $d;',
            '<?php [$a] = $c === 11 ? $b : $d;',
        ];

        yield [
            '<?php $b = [$a] = 7 === [7];', // makes no sense, but valid PHP syntax
            '<?php $b = [$a] = [7] === 7;',
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideWithConfigCases
     */
    public function testWithConfig(array $config, string $expected): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected);
    }

    public static function provideWithConfigCases(): iterable
    {
        yield [
            [
                'identical' => false,
            ],
            '<?php
$a = [1, 2, 3];
while (2 !== $b = array_pop($c));
',
        ];

        yield [
            [
                'equal' => false,
                'identical' => false,
            ],
            '<?php
                if ($revision->event == \'created\') {
    foreach ($revision->getModified() as $col => $data) {
        $model->$col = $data[\'new\'];
    }
} else {
    foreach ($revision->getModified() as $col => $data) {
        $model->$col = $data[\'old\'];
    }
}',
        ];
    }

    /**
     * @dataProvider provideFixPhp74Cases
     */
    public function testFixPhp74(string $expected, ?string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPhp74Cases(): iterable
    {
        yield [
            '<?php if (1_000 === $b);',
            '<?php if ($b === 1_000);',
        ];
    }

    /**
     * Test with the inverse config.
     *
     * @param array<string, mixed> $configuration
     *
     * @dataProvider providePHP74CasesInverseCases
     */
    public function testPHP74CasesInverse(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function providePHP74CasesInverseCases(): iterable
    {
        yield [
            '<?php fn() => $c === array(1) ? $b : $d;',
            null,
            [
                'less_and_greater' => false,
            ],
        ];

        yield [
            '<?php $a ??= 4 === $b ? 2 : 3;',
            '<?php $a ??= $b === 4 ? 2 : 3;',
        ];
    }

    /**
     * @dataProvider provideFixPrePHP80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPrePHP80Cases(): iterable
    {
        yield [
            '<?php return \A/*5*/\/*6*/B\/*7*/C::MY_CONST === \A/*1*//*1*//*1*//*1*//*1*/\/*2*/B/*3*/\C/*4*/::$myVariable;',
            '<?php return \A/*1*//*1*//*1*//*1*//*1*/\/*2*/B/*3*/\C/*4*/::$myVariable === \A/*5*/\/*6*/B\/*7*/C::MY_CONST;',
        ];

        yield [
            '<?php return A\/**//**//**/B/*a*//*a*//*a*//*a*/::MY_CONST === B\C::$myVariable;',
            '<?php return B\C::$myVariable === A\/**//**//**/B/*a*//*a*//*a*//*a*/::MY_CONST;',
        ];

        yield ['<?php return $foo === $bar[$baz]{1};'];

        yield ['<?php return $foo->$a[1] === $bar[$baz]{1}->$a[1][2][3]->$d[$z]{1};'];

        yield ['<?php return $m->a{2}+1 == 2;'];

        yield ['<?php return $m{2}+1 == 2;'];

        yield [
            '<?php echo 1 === (unset) $a ? 8 : 7;',
            '<?php echo (unset) $a === 1 ? 8 : 7;',
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php
if ($a = true === $obj instanceof (foo())) {
    echo 1;
}',
            '<?php
if ($a = $obj instanceof (foo()) === true) {
    echo 1;
}',
        ];

        yield [
            '<?php $i = $this?->getStuff() === $myVariable;',
            '<?php $i = $myVariable === $this?->getStuff();',
            ['equal' => true, 'identical' => true, 'always_move_variable' => true],
        ];

        yield [
            '<?php 42 === $a->b[5]?->c;',
            '<?php $a->b[5]?->c === 42;',
        ];

        yield [
            '<?php return $this->myObject1?->{$index}+$b === "";',
            null,
            ['equal' => true, 'identical' => true],
        ];

        yield [
            '<?php new Foo(bar: 1 === $var);',
            '<?php new Foo(bar: $var === 1);',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'does not make a lot of sense but is valid syntax, do not break 1' => [
            '<?php $b = strlen( ... ) === $a;',
        ];

        yield 'does not make a lot of sense but is valid syntax, do not break 2' => [
            '<?php $b = $a === strlen( ... );',
        ];
    }
}
