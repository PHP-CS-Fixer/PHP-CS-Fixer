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
 * @author SpacePossum
 */
class NativeFunctionCasingFixerTest extends AbstractFixerTestBase
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
                    echo strtolower("hello 1");
                ',
                '<?php
                    echo STRTOLOWER("hello 1");
                ',
            ),
            array(
                '<?php
                    echo strtolower //a
                        ("hello 2");
                ',
                '<?php
                    echo STRTOLOWER //a
                        ("hello 2");
                ',
            ),
            array(
                '<?php
                    echo strtolower /**/   ("hello 3");
                ',
                '<?php
                    echo STRTOLOWER /**/   ("hello 3");
                ',
            ),
            array(
                '<?php
                    echo \numfmt_format("hello 4");
                ',
                '<?php
                    echo \NUMFMT_format("hello 4");
                ',
            ),
            array(
                '<?php
                    echo "1".\numfmt_format("hello 5");
                ',
                '<?php
                    echo "1".\numfmt_FORMAT("hello 5");
                ',
            ),
            array(
                '<?php
                    public function gettype()
                    {
                        return 1;
                    }',
            ),
            array(
                '<?php
                    new STRTOLOWER();
                ',
            ),
            array(
                '<?php
                    a::STRTOLOWER();
                ',
            ),
            array(
                '<?php
                    $a->STRTOLOWER();
                ',
            ),
        );
    }
}
