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
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(
            [
                'statements' => [
                    'break',
                    'clone',
                    'continue',
                    'echo_print',
                    'return',
                    'switch_case',
                    'yield',
                    'yield_from',
                    'others',
                ],
            ],
        );

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php while ($x) { while ($y) { break 2; } }',
            '<?php while ($x) { while ($y) { break (2); } }',
        ];

        yield [
            '<?php while ($x) { while ($y) { break 2; } }',
            '<?php while ($x) { while ($y) { break(2); } }',
        ];

        yield [
            '<?php while ($x) { while ($y) { continue 2; } }',
            '<?php while ($x) { while ($y) { continue (2); } }',
        ];

        yield [
            '<?php while ($x) { while ($y) { continue 2; } }',
            '<?php while ($x) { while ($y) { continue(2); } }',
        ];

        yield [
            <<<'EOD'
                <?php
                                $var = clone ($obj1 ?: $obj2);
                                $var = clone ($obj1 ? $obj1->getSubject() : $obj2);
                EOD."\n                ",
        ];

        yield [
            '<?php clone $object;',
            '<?php clone ($object);',
        ];

        yield [
            '<?php clone new Foo();',
            '<?php clone (new Foo());',
        ];

        yield [
            <<<'EOD'
                <?php
                                foo(clone $a);
                                foo(clone $a, 1);
                                $a = $b ? clone $b : $c;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                foo(clone($a));
                                foo(clone($a), 1);
                                $a = $b ? clone($b) : $c;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                echo (1 + 2) . $foo;
                                print (1 + 2) . $foo;
                EOD."\n                ",
        ];

        yield [
            '<?php echo (1 + 2) * 10, "\n";',
        ];

        yield [
            '<?php echo (1 + 2) * 10, "\n" ?>',
        ];

        yield [
            '<?php echo "foo" ?>',
            '<?php echo ("foo") ?>',
        ];

        yield [
            '<?php print "foo" ?>',
            '<?php print ("foo") ?>',
        ];

        yield [
            '<?php echo "foo2"; print "foo";',
            '<?php echo ("foo2"); print ("foo");',
        ];

        yield [
            '<?php echo "foo"; print "foo1";',
            '<?php echo("foo"); print("foo1");',
        ];

        yield [
            '<?php echo 2; print 2;',
            '<?php echo(2); print(2);',
        ];

        yield [
            <<<'EOD'
                <?php
                                echo $a ? $b : $c;
                                echo ($a ? $b : $c) ? $d : $e;
                                echo 10 * (2 + 3);
                                echo ("foo"), ("bar");
                                echo my_awesome_function("foo");
                                echo $this->getOutput(1);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                echo ($a ? $b : $c);
                                echo ($a ? $b : $c) ? $d : $e;
                                echo 10 * (2 + 3);
                                echo ("foo"), ("bar");
                                echo my_awesome_function("foo");
                                echo $this->getOutput(1);
                EOD."\n                ",
        ];

        yield [
            '<?php return (1 + 2) * 10;',
            '<?php return ((1 + 2) * 10);',
        ];

        yield [
            '<?php return "prod";',
            '<?php return ("prod");',
        ];

        yield [
            '<?php return $x;',
            '<?php return($x);',
        ];

        yield [
            '<?php return 2;',
            '<?php return(2);',
        ];

        yield [
            '<?php return 2?>',
            '<?php return(2)?>',
        ];

        yield [
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case "prod":
                                        break;
                                }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case "prod":
                                        break;
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case ("prod"):
                                        break;
                                }
                EOD."\n                ",
            'switch_case',
        ];

        yield [
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case $x;
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case($x);
                                }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case 2;
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case(2);
                                }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
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
                EOD."\n                ",
            <<<'EOD'
                <?php
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
                EOD."\n                ",
            'switch_case',
        ];

        yield [
            <<<'EOD'
                <?php while ($x) { while ($y) { break#
                #
                2#
                #
                ; } }
                EOD,
            <<<'EOD'
                <?php while ($x) { while ($y) { break#
                (#
                2#
                )#
                ; } }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                function foo() { yield "prod"; }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                function foo() { yield (1 + 2) * 10; }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                function foo() { yield (1 + 2) * 10; }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo() { yield ((1 + 2) * 10); }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                function foo() { yield "prod"; }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo() { yield ("prod"); }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                function foo() { yield 2; }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo() { yield(2); }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                function foo() { $a = (yield $x); }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo() { $a = (yield($x)); }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                $var = clone ($obj1->getSubject() ?? $obj2);
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideFixAllCases
     */
    public function testFixAll(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(
            [
                'statements' => [
                    'break',
                    'clone',
                    'continue',
                    'echo_print',
                    'return',
                    'switch_case',
                    'yield',
                    'yield_from',
                    'others',
                    'negative_instanceof',
                ],
            ],
        );

        $this->doTest($expected, $input);
    }

    public static function provideFixAllCases(): iterable
    {
        yield '===' => [
            '<?php $at = $a === $b;',
            '<?php $at = ($a) === ($b);',
        ];

        yield 'yield/from fun' => [
            <<<'EOD'
                <?php
                                function foo3() { $a = (yield $x); }
                                function foo4() { yield from (1 + 2) * 10; }

                                function foo5() { yield from "prod"; }
                                function foo6() { $a = (yield $x); }
                                function foo7() { yield from 2; }
                                function foo8() { $a = (yield from $x); }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                function foo3() { $a = (yield $x); }
                                function foo4() { yield from ((1 + 2) * 10); }

                                function foo5() { yield from ("prod"); }
                                function foo6() { $a = (yield($x)); }
                                function foo7() { yield from(2); }
                                function foo8() { $a = (yield from($x)); }
                EOD."\n                ",
        ];

        yield 'clone stuff' => [
            <<<'EOD'
                <?php
                                clone new $f[0];
                                $a1 = clone new foo;
                                $a2 = clone new foo();

                                $a3 = [clone $f];
                                $a4 = [1, clone $f];
                                $a5 = [clone $f, 2];
                                $a6 = [1, clone $f, 2];

                                $c1 = fn() => clone $z;

                                for ( clone new Bar(1+2) ; $i < 100; ++$i) {
                                    $i = foo();
                                }

                                clone $object2[0]->foo()[2] /* 1 */ ?>
                EOD,
            <<<'EOD'
                <?php
                                clone (new $f[0]);
                                $a1 = clone (new foo);
                                $a2 = clone (new foo());

                                $a3 = [clone ($f)];
                                $a4 = [1, clone ($f)];
                                $a5 = [clone ($f), 2];
                                $a6 = [1, clone ($f), 2];

                                $c1 = fn() => clone ($z);

                                for ( clone(new Bar(1+2) ); $i < 100; ++$i) {
                                    $i = foo();
                                }

                                clone ($object2[0]->foo()[2]) /* 1 */ ?>
                EOD,
        ];

        yield 'print unary wrapped sequence' => [
            '<?php $b7 =[ print !$a   ,];',
            '<?php $b7 =[ print !($a)   ,];',
        ];

        yield 'print - sequence' => [
            <<<'EOD'
                <?php
                                $b7 =[ print $a, 1];
                                $b7 =[1, print $a];
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $b7 =[ print ($a), 1];
                                $b7 =[1, print ($a)];
                EOD."\n            ",
        ];

        yield 'print - wrapped block' => [
            '<?php $b7 =[ print $a ];',
            '<?php $b7 =[ print ($a) ];',
        ];

        yield 'print fun' => [
            <<<'EOD'
                <?php
                                for ( print $b+1; foo(); ++$i ) {}
                                for( print $b+1; foo(); ++$i ) {}

                                $b1 = $c[print $e+5];
                                $b2 = [1, print $e++];
                                $b3 = [print $e+9, 1];
                                $b4 = [1, 1 + print $e, 1];

                                $b51 = fn() => print $b."something";
                                $b52 = fn() => print $b."else";

                                $b6 = foo(print $a);
                                $b7 =[ print $a   ,];

                                $b8 =[ print ($a+1) . "1"   ,];
                EOD."\n            ",
            <<<'EOD'
                <?php
                                for ( (print $b+1); foo(); ++$i ) {}
                                for( print ($b+1); foo(); ++$i ) {}

                                $b1 = $c[(print $e+5)];
                                $b2 = [1, (print $e++)];
                                $b3 = [(print $e+9), 1];
                                $b4 = [1, (1 + print $e), 1];

                                $b51 = fn() => (print $b."something");
                                $b52 = fn() => print ($b."else");

                                $b6 = foo(print ($a));
                                $b7 =[ (print ($a))   ,];

                                $b8 =[ (print ($a+1) . "1")   ,];
                EOD."\n            ",
        ];

        yield 'simple' => [
            '<?php $aw?><?php $av;',
            '<?php ($aw)?><?php ($av);',
        ];

        yield 'simple, echo open tag' => [
            '<?= $a;',
            '<?= ($a);',
        ];

        yield '+ (X),' => [
            '<?php $bw = [1 + !!$a, 2 + $b,];',
            '<?php $bw = [1 + !!($a), 2 + ($b),];',
        ];

        yield 'op ns' => [
            '<?php $aq = A\B::c . $d;',
            '<?php $aq = (A\B::c) . $d;',
        ];

        yield 'wrapped FN' => [
            '<?php $fn1 = fn($x) => $x + $y;',
            '<?php $fn1 = fn($x) => ($x + $y);',
        ];

        yield 'wrapped FN 2 with pre and reference' => [
            '<?php $fn1 = fn & ($x) => !$x;',
            '<?php $fn1 = fn & ($x) => !($x);',
        ];

        yield 'wrapped FN with `,`' => [
            <<<'EOD'
                <?php
                                $fn1 = array_map(fn() => 1, $array);
                                $fn2 = array_map($array, fn() => 2);
                                $fn3 = array_map($array, fn() => 3, $array);
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $fn1 = array_map(fn() => (1), $array);
                                $fn2 = array_map($array, fn() => (2));
                                $fn3 = array_map($array, fn() => (3), $array);
                EOD."\n            ",
        ];

        yield 'wrapped FN with return type' => [
            <<<'EOD'
                <?php
                                $fn8 = fn(): int => 123456;
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $fn8 = fn(): int => (123456);
                EOD."\n            ",
        ];

        yield 'wrapped `for` elements' => [
            <<<'EOD'
                <?php
                                for (!$a; $a < 10; ++$a){
                                    echo $a;
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                for (!($a); ($a < 10); (++$a)){
                                    echo $a;
                                }
                EOD."\n            ",
        ];

        $statements = [
            'echo',
            'print',
            'return',
            'throw',
            'yield from',
            'yield',
        ];

        foreach ($statements as $statement) {
            yield $statement.', no op, space' => [
                '<?php function foo(){ '.$statement.' $e; }',
                '<?php function foo(){ '.$statement.' ($e); }',
            ];

            yield $statement.', wrapped op, no space' => [
                '<?php function foo(){;'.$statement.' $e.$f; }',
                '<?php function foo(){;'.$statement.'($e.$f); }',
            ];
        }

        yield 'yield wrapped unary' => [
            '<?php $a = function($a) {yield ++$a;};',
            '<?php $a = function($a) {yield (++$a);};',
        ];

        $constants = [
            '__CLASS__',
            '__DIR__',
            '__FILE__',
            '__FUNCTION__',
            '__LINE__',
            '__METHOD__',
            '__NAMESPACE__',
            '__TRAIT__',
        ];

        foreach ($constants as $constant) {
            yield $constant.'+ op' => [
                sprintf('<?php $a = %s . $b;', $constant),
                sprintf('<?php $a = (%s) . $b;', $constant),
            ];
        }

        yield 'break/continue' => [
            <<<'EOD'
                <?php
                                while(foo() && $f) {
                                    while(bar()) {
                                        if ($a) {
                                            break 2;
                                        } else {
                                            continue 2;
                                        }
                                    }
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                while((foo() && $f)) {
                                    while(bar()) {
                                        if ($a) {
                                            break (2);
                                        } else {
                                            continue (2);
                                        }
                                    }
                                }
                EOD."\n            ",
        ];

        yield 'switch case' => [
            <<<'EOD'
                <?php switch($a) {
                                case 1: echo 1;
                                case !$a; echo 2;
                            }
                EOD,
            <<<'EOD'
                <?php switch($a) {
                                case(1): echo 1;
                                case !($a); echo 2;
                            }
                EOD,
        ];

        yield 'switch case II' => [
            <<<'EOD'
                <?php
                                switch ($x) {
                                    case $a + 1 + 3:
                                        break;
                                    case 1 + $a + 4:
                                        break;
                                }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                switch ($x) {
                                    case ($a) + 1 + 3:
                                        break;
                                    case 1 + ($a) + 4:
                                        break;
                                }
                EOD."\n                ",
        ];

        yield 'bin pre bin' => [
            '<?php $t = 1+ $i +1;',
            '<?php $t = 1+($i)+1;',
        ];

        yield 'bin, (X))' => [
            '<?php $ap = 1 + $a;',
            '<?php $ap = 1 + ($a);',
        ];

        yield 'bin close tag' => [
            '<?php $d + 2; ?>',
            '<?php ($d) + 2; ?>',
        ];

        yield 'bin after open echo' => [
            '<?= $d - 55;',
            '<?= ($d) - 55;',
        ];

        yield 'more bin + sequences combinations' => [
            <<<'EOD'
                <?php
                                $b1 = [1 + $d];
                                $b2 = [1 + $d,];
                                $b3 = [1,1 + $d + 1];
                                $b4 = [1,1 + $d,];
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $b1 = [1 + ($d)];
                                $b2 = [1 + ($d),];
                                $b3 = [1,1 + ($d) + 1];
                                $b4 = [1,1 + ($d),];
                EOD."\n            ",
        ];

        yield 'bin doggybag' => [
            <<<'EOD'
                <?php
                                while(foo()[1]){bar();}
                                $aR . "";
                                $e = [$a] + $b;
                                $f = [$a] + $b[1];
                                $a = $b + !$c + $d;
                                $b = !$a[1]++;
                                $c = $c + !!!(bool) -$a;
                                throw $b . "";
                EOD."\n            ",
            <<<'EOD'
                <?php
                                while((foo())[1]){bar();}
                                ($aR) . "";
                                $e = [$a] + ($b);
                                $f = [$a] + ($b)[1];
                                $a = $b + !($c) + $d;
                                $b = !($a)[1]++;
                                $c = $c + !!!(bool) -($a);
                                throw($b) . "";
                EOD."\n            ",
        ];

        $statements = [
            'while(foo()){echo 1;}',
            ';;;;',
            'echo',
            'print',
            'return',
            'throw',
            '',
        ];

        foreach ($statements as $statement) {
            yield $statement.' (X) bin' => [
                '<?php '.$statement.' $e . "foo";',
                '<?php '.$statement.' ($e) . "foo";',
            ];
        }

        yield 'bin after' => [
            <<<'EOD'
                <?php
                                $d * 5;
                                ; $a - $b;
                                while(foo() + 1 < 100){} $z - 6 ?>
                EOD,
            <<<'EOD'
                <?php
                                ($d) * 5;
                                ; ($a) - $b;
                                while((foo()) + 1 < 100){} ($z) - 6 ?>
                EOD,
        ];

        yield 'bin after throw/return' => [
            <<<'EOD'
                <?php
                                function foo() {
                                    if($b) {
                                        throw $e . "";
                                    }

                                    return ((string)$dd)[1] . "a";
                            }
                EOD,
            <<<'EOD'
                <?php
                                function foo() {
                                    if(($b)) {
                                        throw($e) . "";
                                    }

                                    return(((string)$dd)[1] . "a");
                            }
                EOD,
        ];

        yield 'multiple fixes' => [
            <<<'EOD'
                <?php
                                $a = [];
                                $b = [1, []];
                                foo();
                                while(foo()){
                                    $a = foo2();
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a = ([]);
                                $b = ([1,([])]);
                                (foo());
                                while(foo()){
                                    $a = (foo2());
                                }
                EOD."\n            ",
        ];

        yield 'access' => [
            <<<'EOD'
                <?php
                                $ag = $b->A::$foo;

                                $a1 = $b->$c[1];
                                $a2 = $b->c[1][2];
                                $a3 = $b->$c[1]->$a[2]->${"abc"};
                                $a4 = $b->$c[1][2]->${"abc"}(22);

                                $a5 = $o->$foo();
                                $o->$c[] = 6;
                                $a7 = $o->$c[8]   (7);
                                $a9 = $o->abc($a);
                                $a10 = $o->abc($a)[1];
                                $a11 = $o->{$bar};
                                $a12 = $o->{$c->d}($e)[1](2){$f}->$c[1]()?>
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $ag = (($b)->A::$foo);

                                $a1 = (($b)->$c[1]);
                                $a2 = (($b)->c[1][2]);
                                $a3 = (($b)->$c[1]->$a[2]->${"abc"});
                                $a4 = (($b)->$c[1][2]->${"abc"}(22));

                                $a5 = (($o)->$foo());
                                ($o)->$c[] = 6;
                                $a7 = (($o)->$c[8]   (7));
                                $a9 = (($o)->abc($a));
                                $a10 = (($o)->abc($a)[1]);
                                $a11 = (($o)->{$bar});
                                $a12 = (($o)->{$c->d}($e)[1](2){$f}->$c[1]())?>
                EOD."\n            ",
        ];

        yield 'simple unary `!`' => [
            <<<'EOD'
                <?php
                                $a1 = !$foo;
                                $a2 = +$f;
                                $a3 = -$f;
                                $a4 = @bar();
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $a1 = !($foo);
                                $a2 = +($f);
                                $a3 = -($f);
                                $a4 = @(bar());
                EOD."\n            ",
        ];

        yield 'pre op ! function call' => [
            '<?php $a9 = !foo($a + 1);',
            '<?php $a9 = !(foo($a + 1));',
        ];

        yield 'multiple pre in wrapped array init' => [
            '<?php $d7 = [!!!!!!!$a5];',
            '<?php $d7 = [!!!!!!!($a5)];',
        ];

        yield 'pre op cast' => [
            '<?php $a6 = (bool)$foo;',
            '<?php $a6 = (bool)($foo);',
        ];

        yield 'pre op ! wrapped' => [
            '<?php if (!$z) {echo 1;}',
            '<?php if ((!($z))) {echo 1;}',
        ];

        yield 'crazy unary' => [
            <<<'EOD'
                <?php
                                $b0 = !!!(bool) $a1;
                                $b1 = [!!!(bool) $a2,1];
                                !!!(bool) $a3;
                                !!!(bool) $a4;
                                $b = 1 + (!!!!!!!$a5);
                                $a = !$a[1]++;
                                while(!!!(bool) $a2[1] ){echo 1;}
                                $b = @$a[1];
                                $b = ++$a[1];
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $b0 = !!!(bool) ($a1);
                                $b1 = [!!!(bool) ($a2),1];
                                (!!!(bool) (($a3)));
                                (!!!(bool) ($a4));
                                $b = 1 + (!!!!!!!($a5));
                                $a = !($a)[1]++;
                                while(!!!(bool) ($a2)[1] ){echo 1;}
                                $b = @($a)[1];
                                $b = ++($a)[1];
                EOD."\n            ",
        ];

        yield 'logic &&' => [
            '<?php $arr = $a && $b;',
            '<?php $arr = ($a) && $b;',
        ];

        yield 'bin before' => [
            <<<'EOD'
                <?php
                                $ax = $d + $a;
                                $dx = 1 + $z ?>
                EOD,
            <<<'EOD'
                <?php
                                $ax = $d + ($a);
                                $dx = 1 + ($z) ?>
                EOD,
        ];

        yield 'bin before and after' => [
            <<<'EOD'
                <?php
                                echo 1 + 2 + 3;
                                echo 1.5 + 2.5 + 3.5;
                                echo 1 + $obj->value + 3;
                                echo 1 + Obj::VALUE + 3;
                EOD."\n            ",
            <<<'EOD'
                <?php
                                echo 1 + (2) + 3;
                                echo 1.5 + (2.5) + 3.5;
                                echo 1 + ($obj->value) + 3;
                                echo 1 + (Obj::VALUE) + 3;
                EOD."\n            ",
        ];

        yield 'new class as sequence element' => [
            '<?php $a7 = [1, new class($a+$b) {}];',
            '<?php $a7 = [1, (new class($a+$b) {})];',
        ];

        yield 'pre @ sequence' => [
            '<?php $a8 = [1, @ ($f.$b)() ];',
            '<?php $a8 = [1, @( ($f.$b)() )];',
        ];

        yield 'inside `{` and `}`' => [
            <<<'EOD'
                <?php
                                while(foo()) { bar(); }
                                while(bar()){foo1();};
                                if($a){foo();}
                EOD."\n            ",
            <<<'EOD'
                <?php
                                while(foo()) { (bar()); }
                                while(bar()){(foo1());};
                                if($a){(foo());}
                EOD."\n            ",
        ];

        yield 'block type dynamic var brace' => [
            '<?php ${$bar};',
            '<?php ${($bar)};',
        ];

        yield 'block type dynamic prop brace' => [
            '<?php $foo->{$bar};',
            '<?php $foo->{($bar)};',
        ];

        yield 'anonymous class wrapped init' => [
            '<?php $a11 = new class(1){};',
            '<?php $a11 = new class((1)){};',
        ];

        yield 'return, new long array notation, no space' => [
            '<?php return array();',
            '<?php return(array());',
        ];

        yield 'block type array square brace' => [
            <<<'EOD'
                <?php
                                echo $a13 = [1];
                                $bX = [1,];
                                $b0 = [1, 2, 3,];
                                $b1 = [$a + 1];
                                $b2 = [-1 + $a];
                                $b3 = [2 + $c];
                EOD."\n            ",
            <<<'EOD'
                <?php
                                echo $a13 = [(1)];
                                $bX = [(1),];
                                $b0 = [(1),(2),(3),];
                                $b1 = [($a) + 1];
                                $b2 = [-1 + ($a)];
                                $b3 = [2 + ($c)];
                EOD."\n            ",
        ];

        yield 'multiple array construct elements' => [
            '<?php echo $a14 = [1, $a(1,$b(3)), 3+4]?>',
            '<?php echo $a14 = [(1),($a(1,$b(3))),(3+4)]?>',
        ];

        yield 'double comma and `)`' => [
            '<?php echo sprintf("%d%s", $e, $f);',
            '<?php echo sprintf("%d%s", ($e), ($f));',
        ];

        yield 'two wrapped function calls' => [
            '<?php foo(); $ap = foo();',
            '<?php (foo()); $ap = (foo());',
        ];

        yield 'wrapped function call, op + call as arg' => [
            '<?php $bk = foo(1 + bar()) ?>',
            '<?php $bk = (foo(1 + bar())) ?>',
        ];

        yield 'wrapped function call, short open, semicolon' => [
            '<?= foo1z() ?>',
            '<?=(foo1z()) ?>',
        ];

        yield 'wrapped function call, short open, close tag' => [
            '<?=   foo2A();',
            '<?=(   foo2A());',
        ];

        yield 'wrapped returns' => [
            '<?php function A($a) {return 1;}',
            '<?php function A($a) {return (1);}',
        ];

        yield 'wrapped returns ops' => [
            '<?php function A($a1,$b2) {return ++$a1+$b2;}',
            '<?php function A($a1,$b2) {return (++$a1+$b2);}',
        ];

        yield 'throws, no space' => [
            '<?php throw $z . 2;',
            '<?php throw($z . 2);',
        ];

        yield 'throws + op, wrapped in {}' => [
            '<?php if (k()) { throw new $a.$b(1,2); } ?>',
            '<?php if (k()) { throw (new $a.$b(1,2)); } ?>',
        ];

        yield 'dynamic class name op' => [
            '<?php $xX = ($d+$e)->test();',
            '<?php $xX = (($d+$e))->test();',
        ];

        yield 'token type changing edge case' => [
            '<?php $y1 = (new Foo())->bar;',
            '<?php $y1 = ((new Foo()))->bar;',
        ];

        yield 'brace class instantiation open, double wrapped, no assign' => [
            '<?php (new Foo())->bar();',
            '<?php (((new Foo())))->bar();',
        ];

        yield 'brace class instantiation open, multiple wrapped' => [
            '<?php $y0 = (new Foo())->bar;',
            '<?php $y0 = (((((new Foo())))))->bar;',
        ];

        yield 'wrapped instance check' => [
            <<<'EOD'
                <?php
                                ; $foo instanceof Foo;
                                ; $foo() instanceof Foo;
                                $l1 = $foo instanceof $z;
                                $l2 = $foo instanceof $z[1];
                                $l3 = [$foo instanceof $z->a[1]];
                                $l4 = [1, $foo instanceof $a[1]->$f];
                                $l5 = [$foo instanceof Foo, 1];
                                $l6 = [1, $foo instanceof Foo, 1];
                                $fn1 = fn($x) => $fx instanceof Foo;
                                for ($foo instanceof Foo ; $i < 1; ++$i) { echo $i; }
                                class foo {
                                    public function bar() {
                                        self instanceof static;
                                        self instanceof self;
                                        $a instanceof static;
                                        self instanceof $a;
                                        $a instanceof self;
                                    }
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                ; ($foo instanceof Foo);
                                ; ($foo() instanceof Foo);
                                $l1 = ($foo instanceof $z);
                                $l2 = ($foo instanceof $z[1]);
                                $l3 = [($foo instanceof $z->a[1])];
                                $l4 = [1, ($foo instanceof $a[1]->$f)];
                                $l5 = [($foo instanceof Foo), 1];
                                $l6 = [1, ($foo instanceof Foo), 1];
                                $fn1 = fn($x) => ($fx instanceof Foo);
                                for (($foo instanceof Foo) ; $i < 1; ++$i) { echo $i; }
                                class foo {
                                    public function bar() {
                                        (self instanceof static);
                                        (self instanceof self);
                                        ($a instanceof static);
                                        (self instanceof $a);
                                        ($a instanceof self);
                                    }
                                }
                EOD."\n            ",
        ];

        yield 'wrapped negative instanceof' => [
            <<<'EOD'
                <?php
                                !$z instanceof $z[1];
                                $z2 = !$foo[1]->b(1)[2] instanceof A\Foo;

                                $z3 = [!$z instanceof Foo\Bar::$a];

                                $z4 = [1, !$z instanceof Foo\Bar::$a];
                                $z5 = [!$z instanceof Foo\Bar::$a, 2];
                                $z6 = [8, !$z instanceof Foo\Bar::$a, 2];

                                for( !$z instanceof Foo\Bar::$a ; $a < 100; ++$a) {
                                    foo();
                                }

                                $c1 = fn() => !$z instanceof $z[1];

                                if (!$x instanceof $v) {
                                    echo 123;
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                !($z instanceof $z[1]);
                                $z2 = !($foo[1]->b(1)[2] instanceof A\Foo);

                                $z3 = [!($z instanceof Foo\Bar::$a)];

                                $z4 = [1, !($z instanceof Foo\Bar::$a)];
                                $z5 = [!($z instanceof Foo\Bar::$a), 2];
                                $z6 = [8, !($z instanceof Foo\Bar::$a), 2];

                                for( !($z instanceof Foo\Bar::$a) ; $a < 100; ++$a) {
                                    foo();
                                }

                                $c1 = fn() => !($z instanceof $z[1]);

                                if (!($x instanceof $v)) {
                                    echo 123;
                                }
                EOD."\n            ",
        ];

        yield 'wrapped negative instanceof 2' => [
            <<<'EOD'
                <?php
                                class foo {
                                    public function bar() {
                                        !self instanceof static;
                                        !self instanceof self;
                                        !$a instanceof static;
                                        !self instanceof $a;
                                        !$a instanceof self;
                                    }
                            }
                EOD,
            <<<'EOD'
                <?php
                                class foo {
                                    public function bar() {
                                        !(self instanceof static);
                                        !(self instanceof self);
                                        !($a instanceof static);
                                        !(self instanceof $a);
                                        !($a instanceof self);
                                    }
                            }
                EOD,
        ];

        yield '(x,y' => [
            '<?php $n = ["".foo(1+2),3];',
            '<?php $n = [("".foo(1+2)),3];',
        ];

        yield 'x,y) = ' => [
            '<?php $m = [$x, $y];',
            '<?php $m = [$x,($y)];',
        ];

        yield '(x,y,x)' => [
            '<?php $aj = [1, "".foo(1+2),3];',
            '<?php $aj = [1,("".foo(1+2)),3];',
        ];

        yield 'block type index square brace' => [
            '<?php $n = $foo[1];',
            '<?php $n = $foo[(1)];',
        ];

        yield 'multiple wrapped call' => [
            <<<'EOD'
                <?php
                                $u = $z($b);
                                $a = bar/*1*/ ($a) /*2*/;
                EOD,
            <<<'EOD'
                <?php
                                $u = $z(($b));
                                $a = bar/*1*/ (((($a)))) /*2*/;
                EOD,
        ];

        yield 'if' => [
            '<?php if ($z) {echo 1;}',
            '<?php if ((($z))) {echo 1;}',
        ];

        yield 'destructuring square brace + comments' => [
            <<<'EOD'
                <?php
                                [/*A*/$a/*B*/] = z();
                                [/*A*/$a/*B*/, $c] = z();
                EOD."\n            ",
            <<<'EOD'
                <?php
                                [/*A*/($a)/*B*/] = z();
                                [/*A*/($a)/*B*/,($c)] = z();
                EOD."\n            ",
        ];

        yield 'multiple fix cases' => [
            <<<'EOD'
                <?php
                                $aF=array(1);$a=array(1);$a=array(1);$a=array(1);
                                $bF=array(1);$b=array(1);$b=array(1);$b=array(1);
                                $cF=array(1);$c=array(1);$c=array(1);$c=array(1);
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $aF=array((1));$a=array((1));$a=array((1));$a=array((1));
                                $bF=array((1));$b=array((1));$b=array((1));$b=array((1));
                                $cF=array((1));$c=array((1));$c=array((1));$c=array((1));
                EOD."\n            ",
        ];

        yield 'multiple fix cases with space inserts' => [
            '<?php $a=$b. 1 .$c;$a=$b. 1 .$c;$a=$b. 1 .$c;$a=$b. 1 .$c;$a=$b. 1 .$c;$a=$b./*1*/1/*2*/.$c;',
            '<?php $a=$b.(1).$c;$a=$b.(1).$c;$a=$b.(1).$c;$a=$b.(1).$c;$a=$b.(1).$c;$a=$b./*1*/(1)/*2*/.$c;',
        ];

        yield 'empty exit/die' => [
            '<?php exit; die;',
            '<?php exit(); die();',
        ];

        yield 'more space around concat handling' => [
            <<<'EOD'
                <?php
                                $s2 = "a". 0.1 . 2 .    "b"    . "c";
                                $f = 'x' .  'y'  . 'z';
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $s2 = "a".(0.1). (2) .    ("b")    . "c";
                                $f = 'x' . ( 'y' ) . 'z';
                EOD."\n            ",
        ];

        $assignOperators = [
            'and equal' => '&=',
            'coalesce_equal' => '??=',
            'concat_equal' => '.=',
            'div equal' => '*=',
            'minus equal' => '!=',
            'mod equal' => '+=',
            'mul equal' => '*=',
            'or equal' => '+=',
            'plus equal' => '+=',
            'pow equal' => '**=',
            'sl equal' => '<<=',
            'sr equal' => '>>=',
            'xor equal' => '^=',
        ];

        foreach ($assignOperators as $assignOperator) {
            yield [
                '<?php $a '.$assignOperator.' $x;',
                '<?php $a '.$assignOperator.' ($x);',
            ];
        }

        yield 'after `}`' => [
            <<<'EOD'
                <?php
                                while(foo()){}  ++$i;
                                for(;;){}++$i;
                                foreach ($a as $b){}++$i;

                                if (foo()) {}++$i;
                                if (foo()) {} else {}++$i;
                                if (foo()) {} elseif(bar()) {}++$i;

                                switch(foo()){case 1: echo 1;}++$i;
                                switch (foo()){
                                    case 1: {}++$i; break;
                                }

                                function Bar(): array {
                                    $i++;
                                    return [];
                                }
                                function Foo1(){}++$i;
                                function & Foo2(){}++$i;

                                class A{}++$i;
                                class A1 extends BD{}++$i;
                                class A2 extends BD implements CE{}++$i;
                                class A3 extends BD implements CE,DE{}++$i;

                                interface B{}++$i;
                                interface B1 extends A{}++$i;
                                interface B2 extends A,B{}++$i;

                                trait X{}++$i;

                                try{}catch(E $e){}$i++;
                                try {} finally {}++$i;
                EOD."\n            ",
            <<<'EOD'
                <?php
                                while(foo()){}  (++$i);
                                for(;;){}(++$i);
                                foreach ($a as $b){}(++$i);

                                if (foo()) {}(++$i);
                                if (foo()) {} else {}(++$i);
                                if (foo()) {} elseif(bar()) {}(++$i);

                                switch(foo()){case 1: echo 1;}(++$i);
                                switch (foo()){
                                    case 1: {}(++$i); break;
                                }

                                function Bar(): array {
                                    ($i++);
                                    return [];
                                }
                                function Foo1(){}(++$i);
                                function & Foo2(){}(++$i);

                                class A{}(++$i);
                                class A1 extends BD{}(++$i);
                                class A2 extends BD implements CE{}(++$i);
                                class A3 extends BD implements CE,DE{}(++$i);

                                interface B{}(++$i);
                                interface B1 extends A{}(++$i);
                                interface B2 extends A,B{}(++$i);

                                trait X{}(++$i);

                                try{}catch(E $e){}($i++);
                                try {} finally {}(++$i);
                EOD."\n            ",
        ];

        yield 'do not fix' => [
            <<<'EOD'
                <?php
                                $b = ($a+=$c);

                                foo();
                                foo(1);
                                foo(1,$a,3);
                                foo(1,3,);
                                foo($a)->$b;
                                ${foo}();
                                foo(+ $a, -$b);
                                $a[1](2);
                                $b = $c();
                                $z = foo(1) + 2;
                                $a = ${'foo'}();
                                $b["foo"]($bar);
                                namespace\func();
                                Y::class();

                                $z = (print 'a') + 2;

                                $fn1 = fn($x) => $x + $y;
                                $d = function() use ($d) {};
                                function provideFixCases(): iterable{}
                                $z = function &($x) {++$x;};

                                final class Foo { public static function bar() { return new static(); } }

                                $a = (print 1) + 1;
                                $b = $c(print $e);
                                $c = foo(print $e);
                                $c2 =[ print !($a + 1)   ,];

                                $a = ($a && !$c) && $b;
                                $a = (1 + foo()) * 2;
                                $a = -($f+$a);
                                $a = !($f ? $a : $b);
                                $a = @( ($f.$b)() . foo() );
                                $a = $b ? ($c)($d + 2) : $d;
                                $d = ($c && $a ? true : false) ? 1 : 2; // PHP Deprecated:  Unparenthesized `a ? b : c ? d : e` is deprecated.
                                $z = $b ? ($c)($d + 2) : $d;
                                $x = $b ? foo($c)[$a](1) : $d;

                                $a = [1,($a.$foo[1])[2]];
                                $b = [ $d[4]($a[1].$b) , ];
                                $c = [$d[4]($a[1].$b)];

                                $a = ($a ? 1 : 2) ? 1 : 2;
                                $a = $a ? ($a ? 1 : 2): 2;
                                $a = $a ? 1 : ($a ? 1 : 2);

                                $a = 1 + ($a + 1) ? 1 : 2;
                                $a = $a ? ($a + 1) + 3 : 2;
                                $a = $a ? 3 + ($a + 1) : 2;
                                $a = $a ? 3 : 1 + ($a + 1);
                                $a = $a ? 3 : ($a + 1) + 2;

                                $b += ($a+=$c);

                                $a = isset( ( (array) $b) [1] );
                                $a =  ( (array) $b) [1] ;

                                exit(4);
                                exit (foo()) + 1;

                                $a = array();
                                $b = new class(){};
                                declare(ticks=1);
                                $d = empty($e);
                                $e = eval($b);
                                list($f) = $f;

                                try {foo();} catch (E $e){}
                                if ($a){ echo 1; } elseif($b) { echo 2; }
                                switch($a) {case 1: echo 1;}

                                foreach ($a1 as $b){}
                                foreach ($a2 as $b => $c){}

                                for ($a =0; $a < 1; --$a){}

                                while(bar()) {}
                                while(!!!(bool) $a1() ){echo 1;}
                                while(  ($a2)(1) ){echo 1;}

                                $d = isset($c);
                                $d = isset($c,$a,$z);
                                $y = isset($foo) && isset($foo2);
                                unset($d);
                                unset($d,$e);

                                // from https://www.php.net/manual/en/language.operators.precedence.php

                                $a = 1;
                                echo $a + ($a++); // echo $a + $a++; // may print either 2 or 3

                                $i = 1;
                                $array[$i] = ($i++); // $array[$i] = $i++; // may set either index 1 or 2

                                $t = 1+($i++)+1;

                                echo (("x minus one equals " . $x) - 1) . ", or so I hope\n";
                                echo "x minus one equals " . ($x-1) . ", or so I hope\n";

                                $bool = (true and false);

                                $a = ($a instanceof MyClass) && true;
                                $a = foo($a instanceof MyClass);
                                $a = $a || ($a instanceof MyClass);

                                $a = clone ($a)[0];

                                // handled by the `include` rule

                                require (__DIR__."/foo2.php");
                                require_once (__DIR__."/foo.php");
                                include ($a);
                                include_once ($b);

                                // halt compiler

                                __halt_compiler();
                EOD."\n            ",
        ];

        yield 'do not fix ' => [
            <<<'EOD'
                <?php
                                SomeClass::{$method}(
                                    $variableA,
                                    $variableB
                                );
                EOD,
        ];

        yield 'do not fix 2' => [
            '<?php SomeClass::{$method}(2) + 1;',
        ];

        yield 'do not fix 3' => [
            '<?php $object::{$method}(...$args);',
        ];

        yield 'alternative syntax is not completely supported' => [
            '<?php if ($a):(++$i); endif;',
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideWithConfigCases
     */
    public function testWithConfig(array $config, string $expected, string $input): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);

        $this->fixer->configure(['statements' => []]);
        $this->doTest($input);
    }

    public static function provideWithConfigCases(): iterable
    {
        yield 'config: break' => [
            ['statements' => ['break']],
            <<<'EOD'
                <?php
                                while (foo()) {
                                    while (foo()) {
                                        break 2;
                                    }
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                while (foo()) {
                                    while (foo()) {
                                        break (2);
                                    }
                                }
                EOD."\n            ",
        ];

        yield 'config: clone' => [
            ['statements' => ['clone']],
            '<?php ; clone $f;',
            '<?php ; clone($f);',
        ];

        yield 'config: continue' => [
            ['statements' => ['continue']],
            <<<'EOD'
                <?php
                                while (foo()) {
                                    while (foo()) {
                                        continue 2;
                                    }
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                while (foo()) {
                                    while (foo()) {
                                        continue (2);
                                    }
                                }
                EOD."\n            ",
        ];

        yield 'config: echo_print' => [
            ['statements' => ['echo_print']],
            <<<'EOD'
                <?php
                                echo 1; print 2;
                EOD."\n            ",
            <<<'EOD'
                <?php
                                echo (1); print (2);
                EOD."\n            ",
        ];

        yield 'config: return' => [
            ['statements' => ['return']],
            '<?php ; return 999 ?>',
            '<?php ; return (999) ?>',
        ];

        yield 'config: switch_case' => [
            ['statements' => ['switch_case']],
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case 22: echo 1;
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case (22): echo 1;
                                }
                EOD."\n            ",
        ];

        yield 'config: yield' => [
            ['statements' => ['yield']],
            '<?php function foo() { yield "prod"; }',
            '<?php function foo() { yield ("prod"); }',
        ];

        yield 'config: yield_from' => [
            ['statements' => ['yield_from']],
            '<?php function foo() { ; yield from $a; }',
            '<?php function foo() { ; yield from ($a); }',
        ];

        yield 'config: negative_instanceof' => [
            ['statements' => ['negative_instanceof']],
            '<?php !$foo instanceof $b;',
            '<?php !($foo instanceof $b);',
        ];

        yield 'config: others' => [
            ['statements' => ['others']],
            '<?php ; ++$v[1];',
            '<?php ; ++($v)[1];',
        ];
    }

    /**
     * @dataProvider providePrePhp8Cases
     *
     * @requires PHP <8.0
     */
    public function testPrePhp8(string $expected, string $input): void
    {
        $this->fixer->configure(
            [
                'statements' => [
                    'break',
                    'clone',
                    'continue',
                    'echo_print',
                    'return',
                    'switch_case',
                    'yield',
                    'yield_from',
                    'others',
                ],
            ],
        );

        $this->doTest($expected, $input);
    }

    public static function providePrePhp8Cases(): iterable
    {
        yield 'block type array index curly brace' => [
            '<?php echo $a12 = $a{1};',
            '<?php echo $a12 = $a{(1)};',
        ];

        yield 'PHP8 bin doggybag' => [
            <<<'EOD'
                <?php
                                $s = $a{$d}(1) + 1;
                                $t = $a{1}(2);
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $s = ($a{$d}(1)) + 1;
                                $t = ($a{1}(2));
                EOD."\n            ",
        ];
    }

    /**
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(
            [
                'statements' => [
                    'break',
                    'clone',
                    'continue',
                    'echo_print',
                    'return',
                    'switch_case',
                    'yield',
                    'yield_from',
                    'others',
                ],
            ],
        );

        $this->doTest($expected, $input);
    }

    public static function provideFixPhp80Cases(): iterable
    {
        yield 'fn throw' => [
            '<?php $triggerError = fn () => throw new MyError();',
            '<?php $triggerError = fn () => (throw new MyError());',
        ];

        yield 'match' => [
            <<<'EOD'
                <?php
                                $r = match ($food) {
                                    'apple' => 'An apple',
                                    'cake' => 'Some cake',
                                };
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $r = match (($food)) {
                                    'apple' => 'An apple',
                                    'cake' => 'Some cake',
                                };
                EOD."\n            ",
        ];

        yield 'wrapped FN with return types' => [
            <<<'EOD'
                <?php
                                $fn8 = fn(): int|bool => 123456;
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $fn8 = fn(): int|bool => (123456);
                EOD."\n            ",
        ];

        yield [
            '<?php echo 1 + $obj?->value + 3;',
            '<?php echo 1 + ($obj?->value) + 3;',
        ];
    }

    /**
     * @dataProvider provideFixPhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testFixPhp81(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(
            [
                'statements' => [
                    'break',
                    'clone',
                    'continue',
                    'echo_print',
                    'return',
                    'switch_case',
                    'yield',
                    'yield_from',
                    'others',
                ],
            ],
        );

        $this->doTest($expected, $input);
    }

    public static function provideFixPhp81Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                enum Foo: string
                                {
                                    case Bar = "do fix";
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                enum Foo: string
                                {
                                    case Bar = ("do fix");
                                }
                EOD."\n            ",
        ];

        yield [
            '<?php echo Number::Two->value;',
            '<?php echo (Number::Two)->value;',
        ];

        yield 'wrapped FN with return types' => [
            <<<'EOD'
                <?php
                                $fn8 = fn(): A&B => new C();
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $fn8 = fn(): A&B => (new C());
                EOD."\n            ",
        ];

        yield 'wrapped FN with return types and ref' => [
            <<<'EOD'
                <?php
                                $fn9 = fn & (): D & E => new F();
                EOD."\n            ",
            <<<'EOD'
                <?php
                                $fn9 = fn & (): D & E => (new F());
                EOD."\n            ",
        ];

        yield [
            <<<'EOD'
                <?php
                enum Suit
                {
                    case Hearts;
                }$i++;

                EOD,
            <<<'EOD'
                <?php
                enum Suit
                {
                    case Hearts;
                }($i++);

                EOD,
        ];
    }
}
