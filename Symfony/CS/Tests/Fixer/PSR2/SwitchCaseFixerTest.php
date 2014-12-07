<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Jian Wu <jianwu1868@gmail.com>
 */
class SwitchCaseFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixIndentationCases
     *
     * @param array $expected
     * @param array $input
     */
    public function testFixIndentation($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixIndentationCases()
    {
        return array(
            array(
                '<?php
    switch (n)
    {
        case label1:
            echo 1;
            echo 2;
            break;
        case 2:
            echo 3;
            break;
        case 3:
            echo 4;
            break;
        case 4:
        default:
            echo 3;
            echo 4;
    }',
                '<?php
    switch (n)
    {
     case label1:
         echo 1;
            echo 2;
        break;
       case 2:
            echo 3;
            break;
        case 3:
            echo 4;
            break;
        case 4:
      default
      :
    echo 3;
            echo 4;}',
            ),
        );
    }
}
