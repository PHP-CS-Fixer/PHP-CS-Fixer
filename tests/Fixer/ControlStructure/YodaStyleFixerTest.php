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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer
 */
final class YodaStyleFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->fixer->configure(['equal' => true, 'identical' => true]);
        $this->doTest($expected, $input);
    }

    /**
     * Test with the inverse config.
     *
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFixInverse($expected, $input = null)
    {
        $this->fixer->configure(['equal' => false, 'identical' => false]);

        if (null === $input) {
            $this->doTest($expected);
        } else {
            $this->doTest($input, $expected);
        }
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return [
            [
                '<?= 1 === $a ? 5 : 7;',
                '<?= $a === 1 ? 5 : 7;',
            ],
            [
                '<?php print 1 === 1343;',
            ],
            [
                '<?php
                echo 3 === $a ? 2 : 4;
                ',
                '<?php
                echo $a === 3 ? 2 : 4;
                ',
            ],
            [
                '<?php 1 === lala($a) ? 1 : 2;',
                '<?php lala($a) === 1 ? 1 : 2;',
            ],
            [
                '<?php 1 === $a::$a ? 1 : 2;',
                '<?php $a::$a === 1 ? 1 : 2;',
            ],
            [
                '<?php 1 === (bool) $a ? 8 : 7;',
                '<?php (bool) $a === 1 ? 8 : 7;',
            ],
            [
                '<?php 1 === new $a ? 1 : 2;',
                '<?php new $a === 1 ? 1 : 2;',
            ],
            [
                '<?php 1 === "a".$a ? 5 : 6;',
                '<?php "a".$a === 1 ? 5 : 6;',
            ],
            [
                '<?php 1 === __DIR__.$a ? 5 : 6;',
                '<?php __DIR__.$a === 1 ? 5 : 6;',
            ],
            [
                '<?php 1 === $a.$b ? 5 : 6;',
                '<?php $a.$b === 1 ? 5 : 6;',
            ],
            [
                '<?php echo 1 === (object) $a ? 8 : 7;',
                '<?php echo (object) $a === 1 ? 8 : 7;',
            ],
            [
                '<?php 1===(real)$a?1:2;',
                '<?php (real)$a===1?1:2;',
            ],
            [
                '<?php echo 1 === (unset) $a ? 8 : 7;',
                '<?php echo (unset) $a === 1 ? 8 : 7;',
            ],
            [
                '<?php echo 1 === (int) $a ? 8 : 7;',
                '<?php echo (int) $a === 1 ? 8 : 7;',
            ],
            [
                '<?php echo 1 === (float) $a ? 8 : 7;',
                '<?php echo (float) $a === 1 ? 8 : 7;',
            ],
            [
                '<?php echo 1 === (string) $a ? 8 : 7;',
                '<?php echo (string) $a === 1 ? 8 : 7;',
            ],
            [
                '<?php echo 1 === (array) $a ? 8 : 7;',
                '<?php echo (array) $a === 1 ? 8 : 7;',
            ],
            [
                '<?php echo 1 === (bool) $a ? 8 : 7;',
                '<?php echo (bool) $a === 1 ? 8 : 7;',
            ],
            [
                '<?php
if ($a = true === $obj instanceof A) {
    echo \'A\';
}',
                '<?php
if ($a = $obj instanceof A === true) {
    echo \'A\';
}',
            ],
            [
                '<?php echo 1 === !!$a ? 8 : 7;',
                '<?php echo !!$a === 1 ? 8 : 7;',
            ],
            [
                '<?php 1 === new $a ? 1 : 2;',
                '<?php new $a === 1 ? 1 : 2;',
            ],
            [
                '<?php $a = 1 === new b ? 1 : 2;',
                '<?php $a = new b === 1 ? 1 : 2;',
            ],
            [
                '<?php $a = 1 === empty($a) ? 1 : 2;',
                '<?php $a = empty($a) === 1 ? 1 : 2;',
            ],
            [
                '<?php $b = 1 === clone $a ? 5 : 9;',
                '<?php $b = clone $a === 1 ? 5 : 9;',
            ],
            [
                '<?php while(1 === $a ? 1 : 2){};',
                '<?php while($a === 1 ? 1 : 2){};',
            ],
            [
                '<?php $a = 1 === include_once $a ? 1 : 2;',
                '<?php $a = include_once $a === 1 ? 1 : 2;',
            ],
            [
                '<?php echo 1 === include $a ? 1 : 2;',
                '<?php echo include $a === 1 ? 1 : 2;',
            ],
            [
                '<?php echo 1 === require_once $a ? 1 : 2;',
                '<?php echo require_once $a === 1 ? 1 : 2;',
            ],
            [
                '<?php echo 1 === require $a ? 1 : 2;',
                '<?php echo require $a === 1 ? 1 : 2;',
            ],
            [
                '<?php switch(1 === $a){
                    case true: echo 1;
                };',
                '<?php switch($a === 1){
                    case true: echo 1;
                };',
            ],
            [
                '<?php echo 1 === $a ? 1 : 2;',
                '<?php echo $a === 1 ? 1 : 2;',
            ],
            // Don't fix cases.
            ['<?php $a = 1 === 1;'],
            ['<?php $b = $b === $c;'],
            ['<?php $c = $$b === $$c;'],
            ['<?php $d = count($this->array[$var]) === $a;'],
            ['<?php $e = $a === count($this->array[$var]);'],
            ['<?php $f = ($a & self::MY_BITMASK) === $a;'],
            ['<?php $g = $a === ($a & self::MY_BITMASK);'],
            ['<?php $h = $this->getStuff() === $myVariable;'],
            ['<?php $i = $myVariable === $this->getStuff();'],
            ['<?php $j = 2 * $myVar % 3 === $a;'],
            ['<?php return $k === 2 * $myVar % 3;'],
            ['<?php $l = $c > 2;'],
            ['<?php return $this->myObject->{$index}+$b === "";'],
            ['<?php return $m[2]+1 == 2;'],
            // https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/693
            ['<?php return array(2) == $o;'],
            ['<?php return $p == array(2);'],
            ['<?php return array($q) == $a;'],
            ['<?php return $r == array($a);'],
            ['<?php $s = ((array(2))) == $a;'],
            ['<?php $t = $a == ((array(2)));'],
            ['<?php list($a) = $c === array(1) ? $b : $d;'],
            ['<?php $b = 7 === list($a) = [7];'],
            ['<?php $a = function(){} === array(0);'],
            ['<?php $z = $n == list($a) = $b;'],
            ['<?php return $n == list($a) = $b;'],
            // Fix cases.
            'Array destruct by ternary.' => [
                '<?php list($a) = 11 === $c ? $b : $d;',
                '<?php list($a) = $c === 11 ? $b : $d;',
            ],
            'Less spacing.' => [
                '<?php $z=2==$a;$b=$c>1&&$c<=10;',
                '<?php $z=$a==2;$b=$c>1&&$c<=10;',
            ],
            'Comments.' => [
                '<?php $z = /**/ /**/2/**/ /**/
                 # aa
                 /**/==/**/$a/***/;',
                '<?php $z = /**/ /**/$a/**/ /**/
                 # aa
                 /**/==/**/2/***/;',
            ],
            [
                '<?php return 2 == ($a)?>',
                '<?php return ($a) == 2?>',
            ],
            [
                '<?php $a = ($c === ((null === $b)));',
                '<?php $a = ($c === (($b === null)));',
            ],
            [
                '<?php return null == $a[2];',
                '<?php return $a[2] == null;',
            ],
            [
                '<?php return "" === $this->myArray[$index];',
                '<?php return $this->myArray[$index] === "";',
            ],
            [
                '<?php return "" === $this->myArray[$index]->/*1*//*2*//*3*/a;',
                '<?php return $this->myArray[$index]->/*1*//*2*//*3*/a === "";',
            ],
            [
                '<?php return "" === $this->myArray[$index]->a;',
                '<?php return $this->myArray[$index]->a === "";',
            ],
            [
                '<?php return "" === $this->myObject->  {$index};',
                '<?php return $this->myObject->  {$index} === "";',
            ],
            [
                '<?php return "" === $this->myObject->{$index}->a;',
                '<?php return $this->myObject->{$index}->a === "";',
            ],
            [
                '<?php return "" === $this->myObject->$index->a;',
                '<?php return $this->myObject->$index->a === "";',
            ],
            [
                '<?php return self::MY_CONST === self::$myVariable;',
                '<?php return self::$myVariable === self::MY_CONST;',
            ],
            [
                '<?php return \A/*5*/\/*6*/B\/*7*/C::MY_CONST === \A/*1*//*1*//*1*//*1*//*1*/\/*2*/B/*3*/\C/*4*/::$myVariable;',
                '<?php return \A/*1*//*1*//*1*//*1*//*1*/\/*2*/B/*3*/\C/*4*/::$myVariable === \A/*5*/\/*6*/B\/*7*/C::MY_CONST;',
            ],
            [
                '<?php return \A\B\C::MY_CONST === \A\B\C::$myVariable;',
                '<?php return \A\B\C::$myVariable === \A\B\C::MY_CONST;',
            ],
            [
                '<?php return A\/**//**//**/B/*a*//*a*//*a*//*a*/::MY_CONST === B\C::$myVariable;',
                '<?php return B\C::$myVariable === A\/**//**//**/B/*a*//*a*//*a*//*a*/::MY_CONST;',
            ],
            [
                '<?php $a = 1 == $$a?>',
                '<?php $a = $$a == 1?>',
            ],
            'Nested case' => [
                '<?php return null === $a[0 === $b ? $c : $d];',
                '<?php return $a[$b === 0 ? $c : $d] === null;',
            ],
            [
                '<?php return null === $this->{null === $a ? "a" : "b"};',
                '<?php return $this->{$a === null ? "a" : "b"} === null;',
            ],
            'Complex code sample.' => [
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
            ],
            [
                '<?php $b = list($a) = 7 === [7];', // makes no sense, but valid PHP syntax
                '<?php $b = list($a) = [7] === 7;',
            ],
            [
                '<?php $a = 1 === function(){};',
                '<?php $a = function(){} === 1;',
            ],
            [
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
            ],
            [
                '<?php $i = 2 === $this/*a*//*b*//*c*//*d*//*e*//*f*/->getStuff();',
                '<?php $i = $this/*a*//*b*//*c*//*d*//*e*//*f*/->getStuff() === 2;',
            ],
            [
                '<?php return "" === $this->myObject->{$index}->/*1*//*2*/b;',
                '<?php return $this->myObject->{$index}->/*1*//*2*/b === "";',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideLessGreaterCases
     */
    public function testFixLessGreater($expected, $input)
    {
        $this->fixer->configure(['less_and_greater' => true]);
        $this->doTest($expected, $input);
    }

    /**
     * Test with the inverse config.
     *
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideLessGreaterCases
     */
    public function testFixLessGreaterInverse($expected, $input)
    {
        $this->fixer->configure(['less_and_greater' => false]);
        $this->doTest($input, $expected);
    }

    /**
     * @return array<string[]>
     */
    public function provideLessGreaterCases()
    {
        return [
            [
                '<?php $a = 3 <= $b;',
                '<?php $a = $b >= 3;',
            ],
            [
                '<?php $a = 3 > $b;',
                '<?php $a = $b < 3;',
            ],
            [
                '<?php $a = (3 > $b) || $d;',
                '<?php $a = ($b < 3) || $d;',
            ],
        ];
    }

    public function testComplexConfiguration()
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
     * @param array  $config
     * @param string $expectedMessage
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfig(array $config, $expectedMessage)
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp(sprintf(
            '#^\[%s\] %s$#',
            $this->fixer->getName(),
            preg_quote($expectedMessage, '#')
        ));

        $this->fixer->configure($config);
    }

    /**
     * @return array
     */
    public function provideInvalidConfigurationCases()
    {
        return [
            [['equal' => 2], 'Invalid configuration: The option "equal" with value 2 is expected to be of type "bool" or "null", but is of type "integer".'],
            [['_invalid_' => true], 'Invalid configuration: The option "_invalid_" does not exist. Defined options are: "equal", "identical", "less_and_greater".'],
        ];
    }

    public function testDefinition()
    {
        $this->assertInstanceOf(\PhpCsFixer\FixerDefinition\FixerDefinitionInterface::class, $this->fixer->getDefinition());
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider providePHP56Cases
     * @requires PHP 5.6
     */
    public function testFixPHP56($expected, $input)
    {
        $this->fixer->configure(['equal' => true, 'identical' => true]);
        $this->doTest($expected, $input);
    }

    /**
     * Test with the inverse config.
     *
     * @param string $expected
     * @param string $input
     *
     * @dataProvider providePHP56Cases
     * @requires PHP 5.6
     */
    public function testFixPHP56Inverse($expected, $input)
    {
        $this->fixer->configure(['equal' => false, 'identical' => false]);
        $this->doTest($input, $expected);
    }

    /**
     * @return array<string, string[]>
     */
    public function providePHP56Cases()
    {
        return [
            '5.6 Simple non-Yoda conditions that need to be fixed' => [
                '<?php $a **= 4 === $b ? 2 : 3;',
                '<?php $a **= $b === 4 ? 2 : 3;',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePHP70Cases
     * @requires PHP 7.0
     */
    public function testPHP70Cases($expected, $input = null)
    {
        $this->fixer->configure(['equal' => true, 'identical' => true]);
        $this->doTest($expected, $input);
    }

    /**
     * Test with the inverse config.
     *
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePHP70Cases
     * @requires PHP 7.0
     */
    public function testPHP70CasesInverse($expected, $input = null)
    {
        $this->fixer->configure(['equal' => false, 'identical' => false]);

        if (null === $input) {
            $this->doTest($expected);
        } else {
            $this->doTest($input, $expected);
        }
    }

    /**
     * @return array<string[]>
     */
    public function providePHP70Cases()
    {
        return [
            ['<?php $a = $b + 1 <=> $d;'],
            [
                '<?php $a = new class(10) extends SomeClass implements SomeInterface {} === $a;/**/',
            ],
            [
                '<?php $a = $b ?? 1 ?? 2 == $d;',
                '<?php $a = $b ?? 1 ?? $d == 2;',
            ],
            [
                '<?php $a = 1 === new class(10) extends SomeClass implements SomeInterface {};/**/',
                '<?php $a = new class(10) extends SomeClass implements SomeInterface {} === 1;/**/',
            ],
            [
                '<?php
function a() {
    for ($i = 1; $i <= 3; $i++) {
        echo yield 1 === $i ? 1 : 2;
    }
}
',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePHP71Cases
     * @requires PHP 7.1
     */
    public function testPHP71Cases($expected, $input = null)
    {
        $this->fixer->configure(['equal' => true, 'identical' => true]);
        $this->doTest($expected, $input);
    }

    /**
     * Test with the inverse config.
     *
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePHP71Cases
     * @requires PHP 7.1
     */
    public function testPHP71CasesInverse($expected, $input = null)
    {
        $this->fixer->configure(['equal' => false, 'identical' => false]);

        if (null === $input) {
            $this->doTest($expected);
        } else {
            $this->doTest($input, $expected);
        }
    }

    /**
     * @return array<string[]>
     */
    public function providePHP71Cases()
    {
        return [
            // no fix cases
            ['<?php list("a" => $a, "b" => $b, "c" => $c) = $c === array(1) ? $b : $d;'],
            ['<?php list(list("x" => $x1, "y" => $y1), list("x" => $x2, "y" => $y2)) = $points;'],
            ['<?php list("first" => list($x1, $y1), "second" => list($x2, $y2)) = $points;'],
            ['<?php [$a, $b, $c] = [1, 2, 3];'],
            ['<?php ["a" => $a, "b" => $b, "c" => $c] = $a[0];'],
            ['<?php list("a" => $a, "b" => $b, "c" => $c) = $c === array(1) ? $b : $d;'],
            ['<?php $b = 7 === [$a] = [7];'], // makes no sense, but valid PHP syntax
            ['<?php $b = 7 === [$a] = [7];'],
            ['<?php [$a] = $c === array(1) ? $b : $d;'],
            ['<?php $b = 7 === [$a] = [7];'],
            ['<?php $z = $n == [$a] = $b;'],
            ['<?php return $n == [$a] = $b;'],
            // fix cases
            [
                '<?php list("a" => $a, "b" => $b, "c" => $c) = 1 === $c ? $b : $d;',
                '<?php list("a" => $a, "b" => $b, "c" => $c) = $c === 1 ? $b : $d;',
            ],
            [
                '<?php list("a" => $a, "b" => $b, "c" => $c) = A::B === $c ? $b : $d;',
                '<?php list("a" => $a, "b" => $b, "c" => $c) = $c === A::B ? $b : $d;',
            ],
            [
                '<?php list( (2 === $c ? "a" : "b") => $b) = ["a" => 7 === $c ? 5 : 1, "b" => 7];',
                '<?php list( ($c === 2 ? "a" : "b") => $b) = ["a" => $c === 7 ? 5 : 1, "b" => 7];',
            ],
            [
                '<?php [ (ABC::A === $c ? "a" : "b") => $b] = ["a" => 7 === $c ? 5 : 1, "b" => 7];',
                '<?php [ ($c === ABC::A ? "a" : "b") => $b] = ["a" => $c === 7 ? 5 : 1, "b" => 7];',
            ],
            'Array destruct by ternary.' => [
                '<?php [$a] = 11 === $c ? $b : $d;',
                '<?php [$a] = $c === 11 ? $b : $d;',
            ],
                        [
                '<?php $b = [$a] = 7 === [7];', // makes no sense, but valid PHP syntax
                '<?php $b = [$a] = [7] === 7;',
            ],
        ];
    }
}
