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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\IncrementStyleFixer
 */
final class IncrementStyleFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixPreIncrementCases
     */
    public function testFixPreIncrement($expected, $input = null)
    {
        $this->fixer->configure(['style' => 'pre']);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixPostIncrementCases
     */
    public function testFixPostIncrement($expected, $input = null)
    {
        $this->fixer->configure(['style' => 'post']);
        $this->doTest($expected, $input);
    }

    public function provideFixPostIncrementCases()
    {
        return array_map(function (array $case) {
            return array_reverse($case);
        }, $this->provideFixPreIncrementCases());
    }

    public function provideFixPreIncrementCases()
    {
        return [
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
            [
                '<?php ++${$a}->{$b."foo"}->bar[$c]->$baz;',
                '<?php ${$a}->{$b."foo"}->bar[$c]->$baz++;',
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
        ];
    }
}
