<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
class LogicalNotOperatorsWithSpacesFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php $i = 0; $i++; ++$i; $foo = ! false || ( ! true);',
                '<?php $i = 0; $i++; ++$i; $foo = !false || (!true);',
            ),
            array(
                '<?php $i = 0; $i--; --$i; $foo = ! false || ($i && ! true);',
                '<?php $i = 0; $i--; --$i; $foo = !false || ($i && !true);',
            ),
            array(
                '<?php $i = 0; $i--; $foo = ! false || ($i && ! /* some comment */true);',
                '<?php $i = 0; $i--; $foo = !false || ($i && !/* some comment */true);',
            ),
            array(
                '<?php $i = 0; $i--; $foo = ! false || ($i && !    true);',
                '<?php $i = 0; $i--; $foo = !false || ($i && !    true);',
            ),
            array(
                '<?php $i = 0; $i--; $foo = ! false || ($i &&    !    true);',
                '<?php $i = 0; $i--; $foo = !false || ($i &&    !    true);',
            ),
        );
    }
}
