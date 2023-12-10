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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Fixer\Operator\LongToShorthandOperatorFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AbstractShortOperatorFixer
 * @covers \PhpCsFixer\Fixer\Operator\LongToShorthandOperatorFixer
 */
final class LongToShorthandOperatorFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'simple I' => [
            '<?php $a += 123;',
            '<?php $a = $a + 123;',
        ];

        yield 'simple II' => [
            '<?php $b[0] *= 789;',
            '<?php $b[0] = ($b[0]) * 789;',
        ];

        yield 'simple III' => [
            '<?php ($b *= 789);',
            '<?php ($b = $b * 789);',
        ];

        yield 'simple IV' => [
            '<?php foo($c /= 1234, 1);',
            '<?php foo($c = $c / 1234, 1);',
        ];

        yield 'simple V' => [
            '<?php foo(1, $x *= 1235, 1);',
            '<?php foo(1, $x = $x * 1235, 1);',
        ];

        yield 'simple II\' array' => [
            '<?php $aa[1] %= 963;',
            '<?php $aa[1] = $aa[1] % 963;',
        ];

        yield 'simple III array' => [
            '<?php $a[1][2] -= 852;',
            '<?php $a[1][2] = $a[1][2] - 852;',
        ];

        yield 'simple IV array' => [
            '<?php {$a[0][1][122] ^= $a;}',
            '<?php {$a[0][1][122] = $a[0][1][122] ^ $a;}',
        ];

        yield [
            '<?php $xa .= $b;',
            '<?php $xa = $xa . $b;',
        ];

        $constants = ['"foo"', "'foo'", '1', '1.1'];

        foreach ($constants as $i => $constant) {
            yield 'c #'.$i => [
                sprintf('<?php $fa .= %s;', $constant),
                sprintf('<?php $fa = $fa . %s;', $constant),
            ];

            yield 'c reverse #'.$i => [
                sprintf('<?php $ga *= %s  ;', $constant),
                sprintf('<?php $ga = %s * $ga;', $constant),
            ];
        }

        foreach (['-', '/', '.', '%'] as $nonCommutativeKind) {
            yield sprintf('non commutative kind "%s"', $nonCommutativeKind) => [
                sprintf('<?php $nck = 5 %s $nck;', $nonCommutativeKind),
            ];
        }

        foreach (['*' => '*=', '|' => '|=', '&' => '&=', '^' => '^='] as $operator => $shortHand) {
            yield sprintf('commutative operator "%s".', $operator) => [
                sprintf('<?php $a3 %s "456"  ;', $shortHand),
                sprintf('<?php $a3 = "456" %s $a3;', $operator),
            ];
        }

        // array index

        yield 'simple I array' => [
            '<?php $ai[1] += 566;',
            '<?php $ai[1] = $ai[1] + 566;',
        ];

        yield 'simple II array' => [
            '<?php $p[1] += 789;',
            '<?php $p[1] = $p[1] + 789;',
        ];

        // minimal and multiple

        yield 'minimal' => [
            '<?php $a += 1;',
            '<?php $a=$a+1;',
        ];

        yield 'minimal, multiple' => [
            '<?php $a += 1;$a += 1;$a += 1;$a += 1;',
            '<?php $a=$a+1;$a=$a+1;$a=$a+1;$a=$a+1;',
        ];

        // test simple with all operators

        $reflection = new \ReflectionClass(LongToShorthandOperatorFixer::class);
        $operators = $reflection->getStaticProperties()['operators'];

        foreach ($operators as $operator => $info) {
            $shortHand = $info[1];

            yield sprintf('Simple test with operator "%s" var/var.', $operator) => [
                sprintf('<?php $a1 %s $b;', $shortHand),
                sprintf('<?php $a1 = $a1 %s $b;', $operator),
            ];

            yield sprintf('Simple test with operator "%s" var/const.', $operator) => [
                sprintf('<?php $a2 %s 1;', $shortHand),
                sprintf('<?php $a2 = $a2 %s 1;', $operator),
            ];
        }

        // odds and ends

        yield [
            '<?php $a4 += ++$b;',
            '<?php $a4 = $a4 + ++$b;',
        ];

        yield [
            '<?php $a5 .=  '.'
<<<EOD
EOD
;',
            '<?php $a5 = $a5 .
<<<EOD
EOD
;',
        ];

        yield [
            '<?php $a6 .=  '.'
<<<\'EOD\'
EOD
?>',
            '<?php $a6 = $a6 .
<<<\'EOD\'
EOD
?>',
        ];

        yield [
            '<?php
                $t += 1;
                $t1 -= 1;
                $t2 *= 1;
                $t3 /= 1;
                $t4 .= /* */  1;',
            '<?php
                $t = ((($t))) + 1;
                $t1 = ($t1) - 1;
                $t2 = $t2 * 1;
                $t3 = ($t3) / 1;
                $t4 = ($t4) /* */ . 1;',
        ];

        // before assignment var

        yield 'minus itself' => [
            '<?php ;$a -= $a;',
            '<?php ;$a = $a - $a;',
        ];

        yield 'after not needed block' => [
            '<?php {echo 1;} $a &= $a;',
            '<?php {echo 1;} $a = $a & $a;',
        ];

        yield 'after if' => [
            '<?php if($z){echo 2;} $a |= $a;',
            '<?php if($z){echo 2;} $a = $a | $a;',
        ];

        yield 'fn minus itself' => [
            '<?php foo(1, $an -= $an);',
            '<?php foo(1, $an = $an - $an);',
        ];

        yield 'simple, before ) I' => [
            '<?php if ($a) $a .= "X"?>',
            '<?php if ($a) $a = $a . "X"?>',
        ];

        yield [
            '<?php
                $a1 /= +$b1;
                $a2 /= -$b2;
            ',
            '<?php
                $a1 = $a1 / +$b1;
                $a2 = $a2 / -$b2;
            ',
        ];

        // do not fix

        yield 'do not fix various' => ['<?php
            $a = ${foo} . 1;
            $a = ${foo}++ + 1;
            $a = $a[1] * 1;
            $a = $a(1 + 2) . 1;
            $a = $a[1][2] . 1;
            $a = $a[1][2][3][foo()][$a++][1+$a][${"foo"}][99] . 1;
            $a = ${foo}++ . 1;
            $a = ($a /* */ /* */ /* */ /* */ + 1 /* */ ) + 1;
            $a = 1 . 1 + foo();
            $a = 1 . foo() + 1;
            $a = 1 . foo();
            $a = 1 . foo(1, ++$a);
            $a = foo() . 1;
            $a = foo(1, ++$a) . 1;
            $a = $a[1] * 1;
            $a[1] = $a[0] * 1;
            $a = $a(1 + 2) . 1;
            foo($b, ${foo} + 1);
            foo($a + 1);
            $a++ + 2;
            2 + $a++;
            $a = 7 . (int) $a;
            $a = (int) $a . 7;
            (int) $a = 7 . (int) $a;
            (int) $a = (int) $a . 7;
            $a = 1 . $a + foo();
            $a = $a instanceof \Foo & $b;
            $a = $a + $b instanceof \Foo;
            $a = $d / $a + $b;
            $d + $a = $a - $e;
            $a = $a >= $b;
            $a[1] = $a[1] instanceof \Foo & $b;
        '];

        yield ['<?php $a = 123 + $a + $c ?>'];

        yield ['<?php $a = $a + 123 + $c ?>'];

        // do not fix; not assignment

        yield ['<?php ($a + 123);'];

        yield ['<?php while(true){$a + 123;}'];

        yield ['<?php $a + 123;'];

        yield ['<?php ; $a + 123;'];

        // do not fix; precedence

        yield [
            '<?php
                $a = 1;
                $b = 3;
                $a = $a + $b ? 1 : 2;
                var_dump($a);

                $a = 1;
                $b = 3;
                $a += $b ? 1 : 2;
                var_dump($a);

                //---------------------

                $a = 2;
                $b = null;
                $a = $a + $b ?? 3;
                var_dump($a);

                $a = 2;
                $b = null;
                $a += $b ?? 3;
                var_dump($a);

                //---------------------

                $a = 3;
                $b = null;
                $a = $a + $b === null ? 3 : 1;
                var_dump($a);

                $a = 3;
                $b = null;
                $a += $b === null ? 3 : 1;
                var_dump($a);

                //---------------------

                $a = $a & $a ^ true;
                $a = $a ^ true & $a;
                $a = 1 . $a + foo();

                //---------------------

                $a = 1;
                $b = false;
                $z = true;

                $a = $a + $b || $z;
                var_dump($a);
            ',
        ];

        yield ['<?php {echo 1;} $a = new class{} & $a;'];

        // reverse

        yield 'simple I reverse' => [
            '<?php $a *= 9988   ?>',
            '<?php $a = 9988 * $a ?>',
        ];

        yield 'simple V, comments, reverse' => [
            '<?php foo(1, /*1*/$x /*2*/*= /*3*/123/*4*//*5*//*6*/, 1);',
            '<?php foo(1, /*1*/$x/*2*/=/*3*/123/*4*/*/*5*/$x/*6*/, 1);',
        ];

        yield 'simple VI, `)`, reverse' => [
            '<?php foo(1, $x *= 123);',
            '<?php foo(1, $x=123*$x);',
        ];

        yield [
            '<?php $a99 .= // foo
<<<EOD
EOD
    ;',
            '<?php $a99 = $a99 . // foo
<<<EOD
EOD
    ;',
        ];

        yield [
            '<?php $a00 .= // foo2
<<<\'EOD\'
EOD
;',
            '<?php $a00 = $a00 . // foo2
<<<\'EOD\'
EOD
;',
        ];

        yield 'do bother with to much mess' => [
            '<?php
                $a = 1 + $a + 2 + $a;
                $a = $a + 1 + $a + 2;
            ',
        ];

        yield [
            '<?php
                $r[1] = [&$r[1]];
                $r[1] = [$r[1],&$r[1]];
            ',
        ];

        yield 'switch case & default' => [
            '<?php
                switch(foo()) {
                    case \'X\':
                        $pX -= 789;
                        break;
                    default:
                        $pY -= $b5;
                }
            ',
            '<?php
                switch(foo()) {
                    case \'X\':
                        $pX = $pX - 789;
                        break;
                    default:
                        $pY = $pY - $b5;
                }
            ',
        ];

        yield 'operator precedence' => [
            '<?php $x = $z ? $b : $a = $a + 123;',
        ];

        yield 'alternative syntax' => [
            '<?php foreach([1, 2, 3] as $i): $a += $i; endforeach;',
            '<?php foreach([1, 2, 3] as $i): $a = $a + $i; endforeach;',
        ];

        yield 'assign and return' => [
            '<?php

class Foo
{
    private int $test = 1;

    public function bar(int $i): int
    {
        return $this->test += $i;
    }
}',
            '<?php

class Foo
{
    private int $test = 1;

    public function bar(int $i): int
    {
        return $this->test = $this->test + $i;
    }
}',
        ];
    }

    /**
     * @requires PHP <8.0
     *
     * @dataProvider provideFixPrePHP80Cases
     */
    public function testFixPrePHP80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPrePHP80Cases(): iterable
    {
        yield [
            '<?php
                $a = $a[1]{2} . 1;
                $a = $a[1]{2}[3][foo()][$a++][1+$a][${"foo"}][99] . 1;
                $a = 1 . $a[1]{2};
                $a = 1 . $a[1]{2}[3][foo()][$a++][1+$a][${"foo"}][99];',
        ];

        yield 'simple I\' array' => [
            '<?php $a[1] += 963;',
            '<?php $a[1] = $a{1} + 963;',
        ];

        yield 'simple II array' => [
            '<?php $a[1]{1} += 852;',
            '<?php $a[1]{1} = $a[1]{1} + 852;',
        ];

        yield 'simple III array' => [
            '<?php $a{7} += 742;',
            '<?php $a{7} = $a[7] + 742;',
        ];

        yield 'simple IV array' => [
            '<?php {$a[0]{1}[1] ^= $azz;} ?>',
            '<?php {$a[0]{1}[1] = $a[0][1]{1} ^ $azz;} ?>',
        ];
    }
}
