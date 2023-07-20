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

use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AbstractIncrementOperatorFixer
 * @covers \PhpCsFixer\Fixer\Operator\IncrementStyleFixer
 */
final class IncrementStyleFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixPreIncrementCases
     */
    public function testFixPreIncrement(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['style' => IncrementStyleFixer::STYLE_PRE]);
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixPostIncrementCases
     */
    public function testFixPostIncrement(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['style' => IncrementStyleFixer::STYLE_POST]);
        $this->doTest($expected, $input);
    }

    public static function provideFixPostIncrementCases(): iterable
    {
        foreach (self::provideFixPreIncrementCases() as $case) {
            yield array_reverse($case);
        }
    }

    public static function provideFixPreIncrementCases(): iterable
    {
        yield [
            '<?php ++$a;',
            '<?php $a++;',
        ];

        yield [
            '<?php ++$$a;',
            '<?php $$a++;',
        ];

        yield [
            '<?php ++${"a"};',
            '<?php ${"a"}++;',
        ];

        yield [
            '<?php --$a;',
            '<?php $a--;',
        ];

        yield [
            '<?php foo(); ++$a;',
            '<?php foo(); $a++;',
        ];

        yield [
            '<?php if (true) { ++$a; }',
            '<?php if (true) { $a++; }',
        ];

        yield [
            '<?php if (true) {} ++$a;',
            '<?php if (true) {} $a++;',
        ];

        yield [
            '<?php for ($i = 0; $i < $count; ++$i) {}',
            '<?php for ($i = 0; $i < $count; $i++) {}',
        ];

        yield [
            '<?php ++$a->foo;',
            '<?php $a->foo++;',
        ];

        yield [
            '<?php ++$a->{"foo"};',
            '<?php $a->{"foo"}++;',
        ];

        yield [
            '<?php ++$a->$b;',
            '<?php $a->$b++;',
        ];

        yield [
            '<?php ++Foo\Bar::$bar;',
            '<?php Foo\Bar::$bar++;',
        ];

        yield [
            '<?php ++$a::$bar;',
            '<?php $a::$bar++;',
        ];

        yield [
            '<?php ++$a[0];',
            '<?php $a[0]++;',
        ];

        yield [
            '<?php ++$a[$b];',
            '<?php $a[$b]++;',
        ];

        yield ['<?php $a = $b++;'];

        yield ['<?php $a + $b++;'];

        yield ['<?php $a++ + $b;'];

        yield ['<?php foo($b++);'];

        yield ['<?php foo($a, $b++);'];

        yield ['<?php $a[$b++];'];

        yield ['<?php echo $a++;'];

        yield ['<?php $a = ++$b;'];

        yield ['<?php $a + ++$b;'];

        yield ['<?php ++$a + $b;'];

        yield ['<?php foo(++$b);'];

        yield ['<?php foo($a, ++$b);'];

        yield ['<?php $a[++$b];'];

        yield ['<?php echo ++$a;'];

        yield ['<?= ++$a;'];

        yield [
            '<?php class Test {
    public function foo() {
        $a = 123;
        ++self::$st;
    }
}',
            '<?php class Test {
    public function foo() {
        $a = 123;
        self::$st++;
    }
}',
        ];

        yield [
            '<?php class Test {
    public function foo() {
        $a = 123;
        ++static::$st;
    }
}',
            '<?php class Test {
    public function foo() {
        $a = 123;
        static::$st++;
    }
}',
        ];

        yield [
            '<?php if ($foo) ++$a;',
            '<?php if ($foo) $a++;',
        ];

        if (\PHP_VERSION_ID < 8_00_00) {
            yield [
                '<?php ++$a->$b::$c->${$d}->${$e}::f(1 + 2 * 3)->$g::$h;',
                '<?php $a->$b::$c->${$d}->${$e}::f(1 + 2 * 3)->$g::$h++;',
            ];

            yield [
                '<?php ++$a{0};',
                '<?php $a{0}++;',
            ];

            yield [
                '<?php ++${$a}->{$b."foo"}->bar[$c]->$baz;',
                '<?php ${$a}->{$b."foo"}->bar[$c]->$baz++;',
            ];
        }
    }
}
