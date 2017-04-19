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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\PreIncrementFixer
 */
final class PreIncrementFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
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
        ];
    }
}
