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
 * @author Javier Spagnoletti <phansys@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\NotOperatorWithSpaceFixer
 */
final class NotOperatorWithSpaceFixerTest extends AbstractFixerTestCase
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
        return [
            [
                '<?php $i = 0; $i++; ++$i; $foo = ! false || ( ! true);',
                '<?php $i = 0; $i++; ++$i; $foo = !false || (!true);',
            ],
            [
                '<?php $i = 0; $i--; --$i; $foo = ! false || ($i && ! true);',
                '<?php $i = 0; $i--; --$i; $foo = !false || ($i && !true);',
            ],
            [
                '<?php $i = 0; $i--; $foo = ! false || ($i && ! /* some comment */true);',
                '<?php $i = 0; $i--; $foo = !false || ($i && !/* some comment */true);',
            ],
            [
                '<?php $i = 0; $i--; $foo = ! false || ($i && !    true);',
                '<?php $i = 0; $i--; $foo = !false || ($i && !    true);',
            ],
            [
                '<?php $i = 0; $i--; $foo = ! false || ($i &&    !    true);',
                '<?php $i = 0; $i--; $foo = !false || ($i &&    !    true);',
            ],
        ];
    }
}
