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

    public function provideFixCases()
    {
        return [
            'Array destruct by ternary.' => [
                '<?php list($a) = $c === 1 ? $b : $d;',
                '<?php list($a) = 1 === $c ? $b : $d;',
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
            ['<?php return $n == list($a) = $b;'],
            // https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/693
            ['<?php return array(2) == $o;'],
            ['<?php return $p == array(2);'],
            ['<?php return array($q) == $a;'],
            ['<?php return $r == array($a);'],
            ['<?php $s = ((array(2))) == $a;'],
            ['<?php $t = $a == ((array(2)));'],
            // Fix cases.
            'Array destruct by ternary.' => [
                '<?php list($a) = $c === 1 ? $b : $d;',
                '<?php list($a) = 1 === $c ? $b : $d;',
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
                '<?php return self::MY_CONST === self::$myVariable;',
                '<?php return self::$myVariable === self::MY_CONST;',
            ],
            [
                '<?php return \A\B\C::MY_CONST === \A\B\C::$myVariable;',
                '<?php return \A\B\C::$myVariable === \A\B\C::MY_CONST;',
            ],
            [
                '<?php return A\/**//**//**/B::MY_CONST === B\C::$myVariable;',
                '<?php return B\C::$myVariable === A\/**//**//**/B::MY_CONST;',
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
     * @dataProvider providePHP7Cases
     * @requires PHP 7.0
     */
    public function testPHP7Cases($expected, $input = null)
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
     * @dataProvider providePHP7Cases
     * @requires PHP 7.0
     */
    public function testPHP7CasesInverse($expected, $input = null)
    {
        $this->fixer->configure(['equal' => false, 'identical' => false]);

        if (null === $input) {
            $this->doTest($expected);
        } else {
            $this->doTest($input, $expected);
        }
    }

    public function providePHP7Cases()
    {
        return [
            [
                '<?php $a = $b ?? 1 ?? 2 == $d;',
                '<?php $a = $b ?? 1 ?? $d == 2;',
            ],
            ['<?php $a = $b + 1 <=> $d;'],
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
     * @dataProvider provideInvalidConfiguration
     * @requires PHPUnit 5.2
     */
    public function testInvalidConfig(array $config, $expectedMessage)
    {
        $this->expectException('\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException');
        $this->expectExceptionMessageRegExp(sprintf(
            '#^\[%s\] %s$#',
            $this->fixer->getName(),
            preg_quote($expectedMessage, '#')
        ));

        $this->fixer->configure($config);
    }

    public function testDefinition()
    {
        $fixer = $this->createFixer();
        $this->assertInstanceOf('PhpCsFixer\FixerDefinition\FixerDefinitionInterface', $fixer->getDefinition());
    }

    public function provideInvalidConfiguration()
    {
        return [
            [['equal' => 2], 'Invalid configuration: The option "equal" with value 2 is expected to be of type "bool" or "null", but is of type "integer".'],
            [['_invalid_' => true], 'Invalid configuration: The option "_invalid_" does not exist. Defined options are: "equal", "identical", "less_and_greater".'],
        ];
    }
}
