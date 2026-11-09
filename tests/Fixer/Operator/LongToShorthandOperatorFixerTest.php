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

use PhpCsFixer\Fixer\AbstractLongToShorthandOperatorFixer;
use PhpCsFixer\Fixer\AbstractShortOperatorFixer;
use PhpCsFixer\Fixer\Operator\LongToShorthandOperatorFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AbstractLongToShorthandOperatorFixer
 * @covers \PhpCsFixer\Fixer\AbstractShortOperatorFixer
 * @covers \PhpCsFixer\Fixer\Operator\LongToShorthandOperatorFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\LongToShorthandOperatorFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(AbstractLongToShorthandOperatorFixer::class)]
#[CoversClass(AbstractShortOperatorFixer::class)]
#[CoversClass(LongToShorthandOperatorFixer::class)]
final class LongToShorthandOperatorFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        // Only a plain variable target is a candidate here; any offset or member
        // access target is left to `long_to_shorthand_operator_for_complex_targets`.
        yield 'string offset target left alone' => [
            '<?php $text[0] = $text[0] & "\x7F";',
        ];

        yield 'array offset target left alone' => [
            '<?php $a[1] = $a[1] + 2;',
        ];

        yield 'nested offset target left alone' => [
            '<?php $a[1][2] = $a[1][2] - 852;',
        ];

        yield 'property target left alone' => [
            '<?php $this->test = $this->test + $i;',
        ];

        yield 'static property target left alone' => [
            '<?php self::$c = self::$c + 1;',
        ];

        yield 'variable-variable target left alone' => [
            '<?php $$name = $$name + 1;',
        ];

        yield 'simple I' => [
            '<?php $a += 123;',
            '<?php $a = $a + 123;',
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

        yield [
            '<?php $xa .= $b;',
            '<?php $xa = $xa . $b;',
        ];

        $constants = ['"foo"', "'foo'", '1', '1.1'];

        foreach ($constants as $i => $constant) {
            yield 'c #'.$i => [
                \sprintf('<?php $fa .= %s;', $constant),
                \sprintf('<?php $fa = $fa . %s;', $constant),
            ];

            yield 'c reverse #'.$i => [
                \sprintf('<?php $ga *= %s  ;', $constant),
                \sprintf('<?php $ga = %s * $ga;', $constant),
            ];
        }

        foreach (['-', '/', '.', '%'] as $nonCommutativeKind) {
            yield \sprintf('non commutative kind "%s"', $nonCommutativeKind) => [
                \sprintf('<?php $nck = 5 %s $nck;', $nonCommutativeKind),
            ];
        }

        foreach (['*' => '*=', '|' => '|=', '&' => '&=', '^' => '^='] as $operator => $shortHand) {
            yield \sprintf('commutative operator "%s".', $operator) => [
                \sprintf('<?php $a3 %s "456"  ;', $shortHand),
                \sprintf('<?php $a3 = "456" %s $a3;', $operator),
            ];
        }

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
        $operators = \Closure::bind(static fn (): array => AbstractLongToShorthandOperatorFixer::OPERATORS, null, AbstractLongToShorthandOperatorFixer::class)();

        foreach ($operators as $operator => $info) {
            $shortHand = $info[1];

            yield \sprintf('Simple test with operator "%s" var/var.', $operator) => [
                \sprintf('<?php $a1 %s $b;', $shortHand),
                \sprintf('<?php $a1 = $a1 %s $b;', $operator),
            ];

            yield \sprintf('Simple test with operator "%s" var/const.', $operator) => [
                \sprintf('<?php $a2 %s 1;', $shortHand),
                \sprintf('<?php $a2 = $a2 %s 1;', $operator),
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

        // do not fix; not assignment / precedence

        yield ['<?php $a = 123 + $a + $c ?>'];

        yield ['<?php $a = $a + 123 + $c ?>'];

        yield ['<?php ($a + 123);'];

        yield ['<?php $a + 123;'];

        yield [
            '<?php
                $a = 1;
                $b = 3;
                $a = $a + $b ? 1 : 2;

                $a = 2;
                $b = null;
                $a = $a + $b ?? 3;
            ',
        ];

        // reverse

        yield 'simple I reverse' => [
            '<?php $a *= 9988   ?>',
            '<?php $a = 9988 * $a ?>',
        ];

        yield 'simple V, comments, reverse' => [
            '<?php foo(1, /*1*/$x /*2*/*= /*3*/123/*4*//*5*//*6*/, 1);',
            '<?php foo(1, /*1*/$x/*2*/=/*3*/123/*4*/*/*5*/$x/*6*/, 1);',
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
    }
}
