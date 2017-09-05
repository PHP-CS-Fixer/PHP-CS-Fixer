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
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php ++$a;',
                '<?php $a++;',
            ),
            array(
                '<?php ++$$a;',
                '<?php $$a++;',
            ),
            array(
                '<?php ++${"a"};',
                '<?php ${"a"}++;',
            ),
            array(
                '<?php --$a;',
                '<?php $a--;',
            ),
            array(
                '<?php foo(); ++$a;',
                '<?php foo(); $a++;',
            ),
            array(
                '<?php if (true) { ++$a; }',
                '<?php if (true) { $a++; }',
            ),
            array(
                '<?php if (true) {} ++$a;',
                '<?php if (true) {} $a++;',
            ),
            array(
                '<?php for ($i = 0; $i < $count; ++$i) {}',
                '<?php for ($i = 0; $i < $count; $i++) {}',
            ),
            array(
                '<?php ++$a->foo;',
                '<?php $a->foo++;',
            ),
            array(
                '<?php ++$a->{"foo"};',
                '<?php $a->{"foo"}++;',
            ),
            array(
                '<?php ++$a->$b;',
                '<?php $a->$b++;',
            ),
            array(
                '<?php ++Foo\Bar::$bar;',
                '<?php Foo\Bar::$bar++;',
            ),
            array(
                '<?php ++$a::$bar;',
                '<?php $a::$bar++;',
            ),
            array(
                '<?php ++$a[0];',
                '<?php $a[0]++;',
            ),
            array(
                '<?php ++$a[$b];',
                '<?php $a[$b]++;',
            ),
            array(
                '<?php ++${$a}->{$b."foo"}->bar[$c]->$baz;',
                '<?php ${$a}->{$b."foo"}->bar[$c]->$baz++;',
            ),

            array('<?php $a = $b++;'),
            array('<?php $a + $b++;'),
            array('<?php $a++ + $b;'),
            array('<?php foo($b++);'),
            array('<?php foo($a, $b++);'),
            array('<?php $a[$b++];'),
            array('<?php echo $a++;'),
        );
    }
}
