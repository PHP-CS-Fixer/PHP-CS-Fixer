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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AbstractIncrementOperatorFixer
 * @covers \PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer>
 *
 * @author ntzm
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class StandardizeIncrementFixerTest extends AbstractFixerTestCase
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
            '<?php ++$i;',
            '<?php $i += 1;',
        ];

        yield [
            '<?php ++$i;',
            '<?php $i+=1;',
        ];

        yield [
            '<?php for ($i = 0; $i < $n; ++$i) {};',
            '<?php for ($i = 0; $i < $n; $i += 1) {};',
        ];

        yield [
            '<?php ++$foo->bar;',
            '<?php $foo->bar += 1;',
        ];

        yield [
            '<?php ++$foo->$bar;',
            '<?php $foo->$bar += 1;',
        ];

        yield [
            '<?php ++$foo->$$$bar;',
            '<?php $foo->$$$bar += 1;',
        ];

        yield [
            '<?php ++$foo["bar"];',
            '<?php $foo["bar"] += 1;',
        ];

        yield [
            '<?php ++$foo[baz()];',
            '<?php $foo[baz()] += 1;',
        ];

        yield [
            '<?php ++$foo[$bar->baz];',
            '<?php $foo[$bar->baz] += 1;',
        ];

        yield [
            '<?php ++$foo[$bar];',
            '<?php $foo[$bar] += 1;',
        ];

        yield [
            '<?php ++$foo[Bar::BAZ];',
            '<?php $foo[Bar::BAZ] += 1;',
        ];

        yield [
            '<?php echo $foo[++$i];',
            '<?php echo $foo[$i += 1];',
        ];

        yield [
            '<?php echo ++$foo[$bar[$baz]];',
            '<?php echo $foo[$bar[$baz]] += 1;',
        ];

        yield [
            '<?php ++$$foo;',
            '<?php $$foo += 1;',
        ];

        yield [
            '<?php ++$$$$foo;',
            '<?php $$$$foo += 1;',
        ];

        yield [
            '<?php ++${$foo};',
            '<?php ${$foo} += 1;',
        ];

        yield [
            '<?php ++$$${$foo};',
            '<?php $$${$foo} += 1;',
        ];

        yield [
            '<?php ++$a[$b];',
            '<?php $a[$b] += 1;',
        ];

        yield [
            '<?php ++$a[++$b];',
            '<?php $a[$b += 1] += 1;',
        ];

        yield [
            '<?php foo(++$a);',
            '<?php foo($a += 1);',
        ];

        yield [
            '<?php foo(++$a, $bar);',
            '<?php foo($a += 1, $bar);',
        ];

        yield [
            '<?php ++$foo->{++$a};',
            '<?php $foo->{$a += 1} += 1;',
        ];

        yield [
            '<?php ++${++$a};',
            '<?php ${$a += 1} += 1;',
        ];

        yield [
            '<?php ++$i ?>',
            '<?php $i += 1 ?>',
        ];

        yield [
            '<?php $a = $b ? ++$c : ++$d;',
            '<?php $a = $b ? $c += 1 : $d += 1;',
        ];

        yield [
            '<?php ++$a->{++$b}[++$c];',
            '<?php $a->{$b += 1}[$c += 1] += 1;',
        ];

        yield [
            '<?php (++$i);',
            '<?php ($i += 1);',
        ];

        yield [
            '<?php (((++$i)));',
            '<?php ((($i += 1)));',
        ];

        yield [
            '<?php ++$a->b->$c;',
            '<?php $a->b->$c += 1;',
        ];

        yield [
            '<?php ++$i/* foo */;',
            '<?php $i +=/* foo */1;',
        ];

        yield [
            '<?php ++$i/* foo *//* bar */;',
            '<?php $i /* foo */ += /* bar */1;',
        ];

        yield [
            '<?php ++$i/** foo *//** bar */;',
            '<?php $i /** foo */ += /** bar */1;',
        ];

        yield [
            "<?php ++\$i// foo\n;",
            "<?php \$i += // foo\n1;",
        ];

        yield [
            '<?php --$i;',
            '<?php $i -= 1;',
        ];

        yield [
            '<?php --$i;',
            '<?php $i-=1;',
        ];

        yield [
            '<?php for ($i = 0; $i < $n; --$i) {};',
            '<?php for ($i = 0; $i < $n; $i -= 1) {};',
        ];

        yield [
            '<?php --$foo->bar;',
            '<?php $foo->bar -= 1;',
        ];

        yield [
            '<?php --$foo->$bar;',
            '<?php $foo->$bar -= 1;',
        ];

        yield [
            '<?php --$foo->$$$bar;',
            '<?php $foo->$$$bar -= 1;',
        ];

        yield [
            '<?php --$foo["bar"];',
            '<?php $foo["bar"] -= 1;',
        ];

        yield [
            '<?php --$foo[baz()];',
            '<?php $foo[baz()] -= 1;',
        ];

        yield [
            '<?php --$foo[$bar->baz];',
            '<?php $foo[$bar->baz] -= 1;',
        ];

        yield [
            '<?php --$foo[$bar];',
            '<?php $foo[$bar] -= 1;',
        ];

        yield [
            '<?php --$foo[Bar::BAZ];',
            '<?php $foo[Bar::BAZ] -= 1;',
        ];

        yield [
            '<?php echo $foo[--$i];',
            '<?php echo $foo[$i -= 1];',
        ];

        yield [
            '<?php echo --$foo->{$bar};',
            '<?php echo $foo->{$bar} -= 1;',
        ];

        yield [
            '<?php echo --$foo->{$bar->{$baz}};',
            '<?php echo $foo->{$bar->{$baz}} -= 1;',
        ];

        yield [
            '<?php echo --$foo[$bar[$baz]];',
            '<?php echo $foo[$bar[$baz]] -= 1;',
        ];

        yield [
            '<?php --$$foo;',
            '<?php $$foo -= 1;',
        ];

        yield [
            '<?php --$$$$foo;',
            '<?php $$$$foo -= 1;',
        ];

        yield [
            '<?php --${$foo};',
            '<?php ${$foo} -= 1;',
        ];

        yield [
            '<?php --$$${$foo};',
            '<?php $$${$foo} -= 1;',
        ];

        yield [
            '<?php --$a[$b];',
            '<?php $a[$b] -= 1;',
        ];

        yield [
            '<?php --$a[--$b];',
            '<?php $a[$b -= 1] -= 1;',
        ];

        yield [
            '<?php foo(--$a);',
            '<?php foo($a -= 1);',
        ];

        yield [
            '<?php foo(--$a, $bar);',
            '<?php foo($a -= 1, $bar);',
        ];

        yield [
            '<?php --$foo->{--$a};',
            '<?php $foo->{$a -= 1} -= 1;',
        ];

        yield [
            '<?php --${--$a};',
            '<?php ${$a -= 1} -= 1;',
        ];

        yield [
            '<?php --$i ?>',
            '<?php $i -= 1 ?>',
        ];

        yield [
            '<?php $a = $b ? --$c : --$d;',
            '<?php $a = $b ? $c -= 1 : $d -= 1;',
        ];

        yield [
            '<?php --$a->{--$b}[--$c];',
            '<?php $a->{$b -= 1}[$c -= 1] -= 1;',
        ];

        yield [
            '<?php (--$i);',
            '<?php ($i -= 1);',
        ];

        yield [
            '<?php (((--$i)));',
            '<?php ((($i -= 1)));',
        ];

        yield [
            '<?php --$a->b->$c;',
            '<?php $a->b->$c -= 1;',
        ];

        yield [
            '<?php --$i/* foo */;',
            '<?php $i -=/* foo */1;',
        ];

        yield [
            '<?php --$i/* foo *//* bar */;',
            '<?php $i /* foo */ -= /* bar */1;',
        ];

        yield [
            '<?php --$i/** foo *//** bar */;',
            '<?php $i /** foo */ -= /** bar */1;',
        ];

        yield [
            "<?php --\$i// foo\n;",
            "<?php \$i -= // foo\n1;",
        ];

        yield [
            '<?php $i + 1;',
        ];

        yield [
            '<?php $i - 1;',
        ];

        yield [
            '<?php $i = 1;',
        ];

        yield [
            '<?php $i = -1;',
        ];

        yield [
            '<?php $i += 1.0;',
        ];

        yield [
            '<?php $i += "1";',
        ];

        yield [
            '<?php $i -= 1.0;',
        ];

        yield [
            '<?php $i -= "1";',
        ];

        yield [
            '<?php $i += 1 * 2;',
        ];

        yield [
            '<?php $i += 1 ** 2;',
        ];

        yield [
            '<?php $i += 1 / 2;',
        ];

        yield [
            '<?php $i += 1 + 2;',
        ];

        yield [
            '<?php $i += 1 - 2;',
        ];

        yield [
            '<?php $i += 1 % 2;',
        ];

        yield [
            '<?php $i += 1 ?: 2;',
        ];

        yield [
            '<?php $i += 1 & 2;',
        ];

        yield [
            '<?php $i += 1 ^ 2;',
        ];

        yield [
            '<?php $i += 1 >> 2;',
        ];

        yield [
            '<?php $i += 1 << 2;',
        ];

        yield [
            '<?php $i += 1 && true;',
        ];

        yield [
            '<?php $i += 1 || true;',
        ];

        yield [
            '<?php $i += 1 and true;',
        ];

        yield [
            '<?php $i += 1 or true;',
        ];

        yield [
            '<?php $i += 1 xor true;',
        ];

        yield [
            '<?php $i += 1 === 2;',
        ];

        yield [
            '<?php $i += 1 == 2;',
        ];

        yield [
            '<?php $i += 1 !== 2;',
        ];

        yield [
            '<?php $i += 1 != 2;',
        ];

        yield [
            '<?php $i += 1 < 2;',
        ];

        yield [
            '<?php $i += 1 > 2;',
        ];

        yield [
            '<?php $i += 1 <= 2;',
        ];

        yield [
            '<?php $i += 1 >= 2;',
        ];

        yield [
            '<?php $i += 1 <> 2;',
        ];

        yield [
            '<?php $i -= 1 * 2;',
        ];

        yield [
            '<?php $i -= 1 ** 2;',
        ];

        yield [
            '<?php $i -= 1 / 2;',
        ];

        yield [
            '<?php $i -= 1 + 2;',
        ];

        yield [
            '<?php $i -= 1 - 2;',
        ];

        yield [
            '<?php $i -= 1 % 2;',
        ];

        yield [
            '<?php $i -= 1 ?: 2;',
        ];

        yield [
            '<?php $i -= 1 & 2;',
        ];

        yield [
            '<?php $i -= 1 ^ 2;',
        ];

        yield [
            '<?php $i -= 1 >> 2;',
        ];

        yield [
            '<?php $i -= 1 << 2;',
        ];

        yield [
            '<?php $i -= 1 && true;',
        ];

        yield [
            '<?php $i -= 1 || true;',
        ];

        yield [
            '<?php $i -= 1 and true;',
        ];

        yield [
            '<?php $i -= 1 or true;',
        ];

        yield [
            '<?php $i -= 1 xor true;',
        ];

        yield [
            '<?php $i -= 1 === 2;',
        ];

        yield [
            '<?php $i -= 1 == 2;',
        ];

        yield [
            '<?php $i -= 1 !== 2;',
        ];

        yield [
            '<?php $i -= 1 != 2;',
        ];

        yield [
            '<?php $i -= 1 < 2;',
        ];

        yield [
            '<?php $i -= 1 > 2;',
        ];

        yield [
            '<?php $i -= 1 <= 2;',
        ];

        yield [
            '<?php $i -= 1 >= 2;',
        ];

        yield [
            '<?php $i -= 1 <> 2;',
        ];

        yield [
            '<?php #1
#2
++$i#3
#4
#5
#6
#7
;#8
#9',
            '<?php #1
#2
$i#3
#4
+=#5
#6
1#7
;#8
#9',
        ];

        yield [
            '<?php $a -= ($a -= ($a -= (--$a)));',
            '<?php $a -= ($a -= ($a -= ($a -= 1)));',
        ];

        yield [
            '<?php --$a[foo($d,foo($c))];',
            '<?php $a[foo($d,foo($c))] -= 1;',
        ];

        yield [
            '<?php $i *= 1; ++$i;',
            '<?php $i *= 1; $i += 1;',
        ];

        yield [
            '<?php ++A::$b;',
            '<?php A::$b += 1;',
        ];

        yield [
            '<?php ++\A::$b;',
            '<?php \A::$b += 1;',
        ];

        yield [
            '<?php ++\A\B\C::$d;',
            '<?php \A\B\C::$d += 1;',
        ];

        yield [
            '<?php ++$a::$b;',
            '<?php $a::$b += 1;',
        ];

        yield [
            '<?php ++$a::$b->$c;',
            '<?php $a::$b->$c += 1;',
        ];

        yield [
            '<?php class Foo {
                    public static function bar() {
                        ++self::$v1;
                        ++static::$v2;
                    }
                }',
            '<?php class Foo {
                    public static function bar() {
                        self::$v1 += 1;
                        static::$v2 += 1;
                    }
                }',
        ];

        yield [
            '<?php $i -= 1 ?? 2;',
        ];

        yield [
            '<?php $i += 1 ?? 2;',
        ];

        yield [
            '<?php $i -= 1 <=> 2;',
        ];

        yield [
            '<?php $i += 1 <=> 2;',
        ];

        yield [
            '<?php ++$a::$b::$c;',
            '<?php $a::$b::$c += 1;',
        ];

        yield [
            '<?php ++$a->$b::$c;',
            '<?php $a->$b::$c += 1;',
        ];

        yield [
            '<?php ++$a::${$b}::$c;',
            '<?php $a::${$b}::$c += 1;',
        ];

        yield [
            '<?php ++$a->$b::$c->${$d}->${$e}::f(1 + 2 * 3)->$g::$h;',
            '<?php $a->$b::$c->${$d}->${$e}::f(1 + 2 * 3)->$g::$h += 1;',
        ];

        yield [
            '<?php $i += 1_0;',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php echo ++$foo->{$bar};',
            '<?php echo $foo->{$bar} += 1;',
        ];

        yield [
            '<?php echo ++$foo->{$bar->{$baz}};',
            '<?php echo $foo->{$bar->{$baz}} += 1;',
        ];

        yield [
            '<?php ++$a{$b};',
            '<?php $a{$b} += 1;',
        ];

        yield [
            '<?php --$a{$b};',
            '<?php $a{$b} -= 1;',
        ];
    }
}
