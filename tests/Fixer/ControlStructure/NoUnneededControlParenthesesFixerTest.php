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

use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer
 */
final class NoUnneededControlParenthesesFixerTest extends AbstractFixerTestCase
{
    private static $defaultStatements;

    public static function setUpBeforeClass(): void
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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, ?string $fixStatement = null): void
    {
        $this->fixerTest($expected, $input, $fixStatement);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php while ($x) { break; }',
            ],
            [
                '<?php while ($x) { while ($y) { break 2; } }',
                '<?php while ($x) { while ($y) { break (2); } }',
            ],
            [
                '<?php while ($x) { while ($y) { break 2; } }',
                '<?php while ($x) { while ($y) { break(2); } }',
            ],
            [
                '<?php while ($x) { continue; }',
            ],
            [
                '<?php while ($x) { while ($y) { continue 2; } }',
                '<?php while ($x) { while ($y) { continue (2); } }',
            ],
            [
                '<?php while ($x) { while ($y) { continue 2; } }',
                '<?php while ($x) { while ($y) { continue(2); } }',
            ],
            [
                '<?php
                clone $object;
                ',
            ],
            [
                '<?php
                clone new Foo();
                ',
            ],
            [
                '<?php
                $var = clone ($obj1 ?: $obj2);
                ',
            ],
            [
                '<?php
                $var = clone ($obj1 ? $obj1->getSubject() : $obj2);
                ',
            ],
            [
                '<?php
                clone $object;
                ',
                '<?php
                clone ($object);
                ',
            ],
            [
                '<?php
                clone new Foo();
                ',
                '<?php
                clone (new Foo());
                ',
            ],
            [
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
            ],
            [
                '<?php
                echo "foo";
                print "foo";
                ',
            ],
            [
                '<?php
                echo (1 + 2) . $foo;
                print (1 + 2) . $foo;
                ',
            ],
            [
                '<?php
                echo (1 + 2) * 10, "\n";
                ',
            ],
            [
                '<?php echo (1 + 2) * 10, "\n" ?>',
            ],
            [
                '<?php echo "foo" ?>',
                '<?php echo ("foo") ?>',
            ],
            [
                '<?php print "foo" ?>',
                '<?php print ("foo") ?>',
            ],
            [
                '<?php
                echo "foo";
                print "foo";
                ',
                '<?php
                echo ("foo");
                print ("foo");
                ',
            ],
            [
                '<?php
                echo "foo";
                print "foo";
                ',
                '<?php
                echo("foo");
                print("foo");
                ',
            ],
            [
                '<?php
                echo 2;
                print 2;
                ',
                '<?php
                echo(2);
                print(2);
                ',
            ],
            [
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
            ],
            [
                '<?php
                return "prod";
                ',
            ],
            [
                '<?php
                return (1 + 2) * 10;
                ',
            ],
            [
                '<?php
                return (1 + 2) * 10;
                ',
                '<?php
                return ((1 + 2) * 10);
                ',
            ],
            [
                '<?php
                return "prod";
                ',
                '<?php
                return ("prod");
                ',
            ],
            [
                '<?php
                return $x;
                ',
                '<?php
                return($x);
                ',
            ],
            [
                '<?php
                return 2;
                ',
                '<?php
                return(2);
                ',
            ],
            [
                '<?php
                return 2?>
                ',
                '<?php
                return(2)?>
                ',
            ],
            [
                '<?php
                switch ($a) {
                    case "prod":
                        break;
                }
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                function foo() { yield "prod"; }
                ',
            ],
            [
                '<?php
                function foo() { yield (1 + 2) * 10; }
                ',
            ],
            [
                '<?php
                function foo() { yield (1 + 2) * 10; }
                ',
                '<?php
                function foo() { yield ((1 + 2) * 10); }
                ',
            ],
            [
                '<?php
                function foo() { yield "prod"; }
                ',
                '<?php
                function foo() { yield ("prod"); }
                ',
            ],
            [
                '<?php
                function foo() { yield 2; }
                ',
                '<?php
                function foo() { yield(2); }
                ',
            ],
            [
                '<?php
                function foo() { $a = (yield $x); }
                ',
                '<?php
                function foo() { $a = (yield($x)); }
                ',
            ],
            [
                '<?php
                $var = clone ($obj1->getSubject() ?? $obj2);
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixYieldFromCases
     */
    public function testFixYieldFrom(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['statements' => ['yield_from']]);
        $this->doTest($expected, $input);
    }

    public function provideFixYieldFromCases()
    {
        return [
            [
                '<?php
                function foo1() { yield from "prod"; }
                ',
            ],
            [
                '<?php
                function foo2() { yield from (1 + 2) * 10; }

                function foo3() { $a = (yield($x)); }
                ',
            ],
            [
                '<?php
                function foo4() { yield from (1 + 2) * 10; }
                ',
                '<?php
                function foo4() { yield from ((1 + 2) * 10); }
                ',
            ],
            [
                '<?php
                function foo5() { yield from "prod"; }
                function foo6() { $a = (yield($x)); }
                ',
                '<?php
                function foo5() { yield from ("prod"); }
                function foo6() { $a = (yield($x)); }
                ',
            ],
            [
                '<?php
                function foo7() { yield from 2; }
                ',
                '<?php
                function foo7() { yield from(2); }
                ',
            ],
            [
                '<?php
                function foo8() { $a = (yield from $x); }
                ',
                '<?php
                function foo8() { $a = (yield from($x)); }
                ',
            ],
        ];
    }

    private function fixerTest(string $expected, ?string $input = null, ?string $fixStatement = null, bool $legacy = false): void
    {
        // Default config. Fixes all statements.
        $this->doTest($expected, $input);

        $this->fixer->configure($legacy ? self::$defaultStatements : ['statements' => self::$defaultStatements]);
        $this->doTest($expected, $input);

        // Empty array config. Should not fix anything.
        $this->fixer->configure([]);
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

            $this->fixer->configure($legacy ? [$statement] : ['statements' => [$statement]]);
            $this->doTest(
                $expected,
                $withInput ? $input : null
            );
        }
    }
}
