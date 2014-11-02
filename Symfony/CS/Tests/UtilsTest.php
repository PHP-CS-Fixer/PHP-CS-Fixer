<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Utils;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCamelCaseToUnderscoreCases
     */
    public function testCamelCaseToUnderscore($expected, $input = null)
    {
        if (null !== $input) {
            $this->assertSame($expected, Utils::camelCaseToUnderscore($input));
        }

        $this->assertSame($expected, Utils::camelCaseToUnderscore($expected));
    }

    public function provideCamelCaseToUnderscoreCases()
    {
        return array(
            array(
                'dollar_close_curly_braces',
                'DollarCloseCurlyBraces',
            ),
        );
    }

    /**
     * @dataProvider provideCmpIntCases
     */
    public function testcmpInt($expected, $left, $right)
    {
        $this->assertSame($expected, Utils::cmpInt($left, $right));
    }

    public function provideCmpIntCases()
    {
        return array(
            array(0,    1,   1),
            array(0,   -1,  -1),
            array(-1,  10,  20),
            array(-1, -20, -10),
            array(1,   20,  10),
            array(1,  -10, -20),
        );
    }
}
