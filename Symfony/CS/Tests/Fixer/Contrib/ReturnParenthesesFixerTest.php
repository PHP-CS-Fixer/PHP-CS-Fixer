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

class ReturnParenthesesFixerTest extends AbstractFixerTestBase
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
                return "prod";
                ',
            ),
            array(
                '<?php
                return (1 + 2) * 10;
                ',
            ),
            array(
                '<?php
                return (1 + 2) * 10;
                ',
                '<?php
                return ((1 + 2) * 10);
                ',
            ),
            array(
                '<?php
                return "prod";
                ',
                '<?php
                return ("prod");
                ',
            ),
        );
    }
}
