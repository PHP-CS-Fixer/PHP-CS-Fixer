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
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
class AlignDoubleArrowFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                $arr = array(
                $a    => 1,
                $bbbb => \'
                $cccccccc = 3;
                \',
                );
                ',
                '<?php
                $arr = array(
                $a => 1,
                $bbbb => \'
                $cccccccc = 3;
                \',
                );
                ',
            ),
            array(
                '<?php
                $arr = [
                $a    => 1,
                $bbbb => \'
                $cccccccc = 3;
                \',
                ];
                ',
                '<?php
                $arr = [
                $a => 1,
                $bbbb => \'
                $cccccccc = 3;
                \',
                ];
                ',
            ),
        );
    }
}
