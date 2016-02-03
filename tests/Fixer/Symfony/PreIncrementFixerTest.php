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

namespace PhpCsFixer\Tests\Fixer\Symfony;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class PreIncrementFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
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
