<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class UnalignEqualsFixerTest extends AbstractFixerTestBase
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
    $a = 1;
    $bbbb = \'
    $cccccccc = 3;
    \';',
                '<?php
    $a    = 1;
    $bbbb = \'
    $cccccccc = 3;
    \';',
            ),
            array(
                '<?php
    $ccc = 1;
    $bb = 1;
    $a = 1;

    /*
    Others alignments
     */
    $a[$b = 1] = 1;
    $ab[$bc = 1] = 1;
    $abc[$bcd = 1] = 1;
    $a[$b] = 1;
    $ab[$bc] = 1;
    $abc[$bcd] = 1;

    if ($a = 1) {
        $ccc = 1;
        $bb = 1;
        $a = 1;
    }

    function a($a = 1, $b = 2, $c = 3)
    {
        $a[$b = 1] = 1;
        $ab[$bc = 1] = 1;
        $abc[$bcd = 1] = 1;
    }

    function b(
        $a = 1,
        $bbb = 2,
        $c = 3
    ) {
        $a[$b = 1] = 1;
        $ab[$bc = 1] = 1;
        $abc[$bcd = 1] = 1;
    }

    while (false) {
        $aa = 2;
        $a[$b] = array();
    }

    for ($i = 0; $i < 10; $i++) {
        $aa = 2;
        $a[$b] = array();
    }',
                '<?php
    $ccc = 1;
    $bb  = 1;
    $a   = 1;

    /*
    Others alignments
     */
    $a[$b = 1]     = 1;
    $ab[$bc = 1]   = 1;
    $abc[$bcd = 1] = 1;
    $a[$b]         = 1;
    $ab[$bc]       = 1;
    $abc[$bcd]     = 1;

    if ($a = 1) {
        $ccc = 1;
        $bb  = 1;
        $a   = 1;
    }

    function a($a = 1, $b = 2, $c = 3)
    {
        $a[$b = 1]     = 1;
        $ab[$bc = 1]   = 1;
        $abc[$bcd = 1] = 1;
    }

    function b(
        $a = 1,
        $bbb = 2,
        $c = 3
    ) {
        $a[$b = 1]     = 1;
        $ab[$bc = 1]   = 1;
        $abc[$bcd = 1] = 1;
    }

    while (false) {
        $aa    = 2;
        $a[$b] = array();
    }

    for ($i = 0; $i < 10; $i++) {
        $aa    = 2;
        $a[$b] = array();
    }',
            ),
        );
    }
}
