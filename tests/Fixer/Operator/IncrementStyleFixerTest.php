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

    public static function provideFixPostIncrementCases(): array
    {
        return array_map(static function (array $case): array {
            return array_reverse($case);
        }, self::provideFixPreIncrementCases());
    }

    public static function provideFixPreIncrementCases(): array
    {
        $cases = [
            [
                '<?php ++$a;',
                '<?php $a++;',
            ],
            [
                '<?php ++$$a;',
                '<?php $$a++;',
            ],
            [
                '<?php ++${"a"};',
                '<?php ${"a"}++;',
            ],
            [
                '<?php --$a;',
                '<?php $a--;',
            ],
            [
                '<?php foo(); ++$a;',
                '<?php foo(); $a++;',
            ],
            [
                '<?php if (true) { ++$a; }',
                '<?php if (true) { $a++; }',
            ],
            [
                '<?php if (true) {} ++$a;',
                '<?php if (true) {} $a++;',
            ],
            [
                '<?php for ($i = 0; $i < $count; ++$i) {}',
                '<?php for ($i = 0; $i < $count; $i++) {}',
            ],
            [
                '<?php ++$a->foo;',
                '<?php $a->foo++;',
            ],
            [
                '<?php ++$a->{"foo"};',
                '<?php $a->{"foo"}++;',
            ],
            [
                '<?php ++$a->$b;',
                '<?php $a->$b++;',
            ],
            [
                '<?php ++Foo\Bar::$bar;',
                '<?php Foo\Bar::$bar++;',
            ],
            [
                '<?php ++$a::$bar;',
                '<?php $a::$bar++;',
            ],
            [
                '<?php ++$a[0];',
                '<?php $a[0]++;',
            ],
            [
                '<?php ++$a[$b];',
                '<?php $a[$b]++;',
            ],

            ['<?php $a = $b++;'],
            ['<?php $a + $b++;'],
            ['<?php $a++ + $b;'],
            ['<?php foo($b++);'],
            ['<?php foo($a, $b++);'],
            ['<?php $a[$b++];'],
            ['<?php echo $a++;'],

            ['<?php $a = ++$b;'],
            ['<?php $a + ++$b;'],
            ['<?php ++$a + $b;'],
            ['<?php foo(++$b);'],
            ['<?php foo($a, ++$b);'],
            ['<?php $a[++$b];'],
            ['<?php echo ++$a;'],
            ['<?= ++$a;'],

            [
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
            ],

            [
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
            ],
            [
                '<?php if ($foo) ++$a;',
                '<?php if ($foo) $a++;',
            ],
        ];

        if (\PHP_VERSION_ID < 8_00_00) {
            $cases[] = [
                '<?php ++$a->$b::$c->${$d}->${$e}::f(1 + 2 * 3)->$g::$h;',
                '<?php $a->$b::$c->${$d}->${$e}::f(1 + 2 * 3)->$g::$h++;',
            ];

            $cases[] = [
                '<?php ++$a{0};',
                '<?php $a{0}++;',
            ];

            $cases[] = [
                '<?php ++${$a}->{$b."foo"}->bar[$c]->$baz;',
                '<?php ${$a}->{$b."foo"}->bar[$c]->$baz++;',
            ];
        }

        return $cases;
    }
}
