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

use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class NoUnneededControlParenthesesFixerTest extends AbstractFixerTestCase
{
    private static $defaultStatements;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $fixer = new NoUnneededControlParenthesesFixer();
        foreach ($fixer->getConfigurationDefinition()->getOptions() as $option) {
            if ('statements' === $option->getName()) {
                self::$defaultStatements = $option->getDefault();
                break;
            }
        }
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|string $fixStatement
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, $fixStatement = null)
    {
        $this->fixerTest($expected, $input, $fixStatement);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|string $fixStatement
     *
     * @group legacy
     * @dataProvider provideFixCases
     * @expectedDeprecation Passing "statements" at the root of the configuration is deprecated and will not be supported in 3.0, use "statements" => array(...) option instead.
     */
    public function testLegacyFix($expected, $input = null, $fixStatement = null)
    {
        $this->fixerTest($expected, $input, $fixStatement, true);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|string $fixStatement
     *
     * @dataProvider provideFixCases55
     * @requires PHP 5.5
     */
    public function testFix55($expected, $input = null, $fixStatement = null)
    {
        $this->fixerTest($expected, $input, $fixStatement);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|string $fixStatement
     *
     * @group legacy
     * @dataProvider provideFixCases55
     * @expectedDeprecation Passing "statements" at the root of the configuration is deprecated and will not be supported in 3.0, use "statements" => array(...) option instead.
     * @requires PHP 5.5
     */
    public function testLegacyFix55($expected, $input = null, $fixStatement = null)
    {
        $this->fixerTest($expected, $input, $fixStatement, true);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|string $fixStatement
     *
     * @dataProvider provideFixCases70
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null, $fixStatement = null)
    {
        $this->fixerTest($expected, $input, $fixStatement);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|string $fixStatement
     *
     * @group legacy
     * @dataProvider provideFixCases70
     * @expectedDeprecation Passing "statements" at the root of the configuration is deprecated and will not be supported in 3.0, use "statements" => array(...) option instead.
     * @requires PHP 7.0
     */
    public function testLegacyFix70($expected, $input = null, $fixStatement = null)
    {
        $this->fixerTest($expected, $input, $fixStatement, true);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php while ($x) { break; }',
            ),
            array(
                '<?php while ($x) { while ($y) { break 2; } }',
                '<?php while ($x) { while ($y) { break (2); } }',
            ),
            array(
                '<?php while ($x) { while ($y) { break 2; } }',
                '<?php while ($x) { while ($y) { break(2); } }',
            ),
            array(
                '<?php while ($x) { continue; }',
            ),
            array(
                '<?php while ($x) { while ($y) { continue 2; } }',
                '<?php while ($x) { while ($y) { continue (2); } }',
            ),
            array(
                '<?php while ($x) { while ($y) { continue 2; } }',
                '<?php while ($x) { while ($y) { continue(2); } }',
            ),
            array(
                '<?php
                clone $object;
                ',
            ),
            array(
                '<?php
                clone new Foo();
                ',
            ),
            array(
                '<?php
                $var = clone ($obj1 ?: $obj2);
                ',
            ),
            array(
                '<?php
                $var = clone ($obj1 ? $obj1->getSubject() : $obj2);
                ',
            ),
            array(
                '<?php
                clone $object;
                ',
                '<?php
                clone ($object);
                ',
            ),
            array(
                '<?php
                clone new Foo();
                ',
                '<?php
                clone (new Foo());
                ',
            ),
            array(
                '<?php
                foo(clone $a);
                foo(clone $a, 1);
                $a = $b ? clone $b : $c;
                ',
                '<?php
                foo(clone($a));
                foo(clone($a), 1);
                $a = $b ? clone($b) : $c;
                ',
            ),
            array(
                '<?php
                echo "foo";
                print "foo";
                ',
            ),
            array(
                '<?php
                echo (1 + 2) . $foo;
                print (1 + 2) . $foo;
                ',
            ),
            array(
                '<?php
                echo (1 + 2) * 10, "\n";
                ',
            ),
            array(
                '<?php echo (1 + 2) * 10, "\n" ?>',
            ),
            array(
                '<?php echo "foo" ?>',
                '<?php echo ("foo") ?>',
            ),
            array(
                '<?php print "foo" ?>',
                '<?php print ("foo") ?>',
            ),
            array(
                '<?php
                echo "foo";
                print "foo";
                ',
                '<?php
                echo ("foo");
                print ("foo");
                ',
            ),
            array(
                '<?php
                echo "foo";
                print "foo";
                ',
                '<?php
                echo("foo");
                print("foo");
                ',
            ),
            array(
                '<?php
                echo 2;
                print 2;
                ',
                '<?php
                echo(2);
                print(2);
                ',
            ),
            array(
                '<?php
                echo $a ? $b : $c;
                echo ($a ? $b : $c) ? $d : $e;
                echo 10 * (2 + 3);
                echo ("foo"), ("bar");
                echo my_awesome_function("foo");
                echo $this->getOutput(1);
                ',
                '<?php
                echo ($a ? $b : $c);
                echo ($a ? $b : $c) ? $d : $e;
                echo 10 * (2 + 3);
                echo ("foo"), ("bar");
                echo my_awesome_function("foo");
                echo $this->getOutput(1);
                ',
            ),
            array(
                '<?php
                return "prod";
                ',
            ),
            array(
                '<?php
                return (1 + 2) * 10;
                ',
            ),
            array(
                '<?php
                return (1 + 2) * 10;
                ',
                '<?php
                return ((1 + 2) * 10);
                ',
            ),
            array(
                '<?php
                return "prod";
                ',
                '<?php
                return ("prod");
                ',
            ),
            array(
                '<?php
                return $x;
                ',
                '<?php
                return($x);
                ',
            ),
            array(
                '<?php
                return 2;
                ',
                '<?php
                return(2);
                ',
            ),
            array(
                '<?php
                return 2?>
                ',
                '<?php
                return(2)?>
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case "prod":
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case "prod":
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case ("prod"):
                        break;
                }
                ',
                'switch_case',
            ),
            array(
                '<?php
                switch ($a) {
                    case $x;
                }
                ',
                '<?php
                switch ($a) {
                    case($x);
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case 2;
                }
                ',
                '<?php
                switch ($a) {
                    case(2);
                }
                ',
            ),
            array(
                '<?php
                $a = 5.1;
                $b = 1.0;
                switch($a) {
                    case (int) $a < 1 : {
                        echo "leave alone";
                        break;
                    }
                    case $a < 2/* test */: {
                        echo "fix 1";
                        break;
                    }
                    case 3 : {
                        echo "fix 2";
                        break;
                    }
                    case /**//**/ // test
                        4
                        /**///
                        /**/: {
                        echo "fix 3";
                        break;
                    }
                    case ((int)$b) + 4.1: {
                        echo "fix 4";
                        break;
                    }
                    case ($b + 1) * 2: {
                        echo "leave alone";
                        break;
                    }
                }
                ',
                '<?php
                $a = 5.1;
                $b = 1.0;
                switch($a) {
                    case (int) $a < 1 : {
                        echo "leave alone";
                        break;
                    }
                    case ($a < 2)/* test */: {
                        echo "fix 1";
                        break;
                    }
                    case (3) : {
                        echo "fix 2";
                        break;
                    }
                    case /**/(/**/ // test
                        4
                        /**/)//
                        /**/: {
                        echo "fix 3";
                        break;
                    }
                    case (((int)$b) + 4.1): {
                        echo "fix 4";
                        break;
                    }
                    case ($b + 1) * 2: {
                        echo "leave alone";
                        break;
                    }
                }
                ',
                'switch_case',
            ),
            array(
                '<?php while ($x) { while ($y) { break#
#
2#
#
; } }',
                '<?php while ($x) { while ($y) { break#
(#
2#
)#
; } }',
            ),
        );
    }

    public function provideFixCases55()
    {
        return array(
            array(
                '<?php
                function foo() { yield "prod"; }
                ',
            ),
            array(
                '<?php
                function foo() { yield (1 + 2) * 10; }
                ',
            ),
            array(
                '<?php
                function foo() { yield (1 + 2) * 10; }
                ',
                '<?php
                function foo() { yield ((1 + 2) * 10); }
                ',
            ),
            array(
                '<?php
                function foo() { yield "prod"; }
                ',
                '<?php
                function foo() { yield ("prod"); }
                ',
            ),
            array(
                '<?php
                function foo() { yield 2; }
                ',
                '<?php
                function foo() { yield(2); }
                ',
            ),
            array(
                '<?php
                function foo() { $a = (yield $x); }
                ',
                '<?php
                function foo() { $a = (yield($x)); }
                ',
            ),
        );
    }

    public function provideFixCases70()
    {
        return array(
            array(
                '<?php
                $var = clone ($obj1->getSubject() ?? $obj2);
                ',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|string $fixStatement
     * @param bool        $legacy
     */
    private function fixerTest($expected, $input = null, $fixStatement = null, $legacy = false)
    {
        // Default config. Fixes all statements.
        $this->doTest($expected, $input);

        $this->fixer->configure($legacy ? self::$defaultStatements : array('statements' => self::$defaultStatements));
        $this->doTest($expected, $input);

        // Empty array config. Should not fix anything.
        $this->fixer->configure(array());
        $this->doTest($expected, null);

        // Test with only one statement
        foreach (self::$defaultStatements as $statement) {
            $withInput = false;

            if ($input && (!$fixStatement || $fixStatement === $statement)) {
                foreach (explode('_', $statement) as $singleStatement) {
                    if (false !== strpos($input, $singleStatement)) {
                        $withInput = true;
                        break;
                    }
                }
            }

            $this->fixer->configure($legacy ? array($statement) : array('statements' => array($statement)));
            $this->doTest(
                $expected,
                $withInput ? $input : null
            );
        }
    }
}
