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
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
class MergeDoubleArrowAndArrayFixerTest extends AbstractFixerTestBase
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
                    $a => array(1)
                );
                ',
                '<?php
                $arr = array(
                    $a =>
                    array(1)
                );
                ',
            ),
            array(
                '<?php
                $arr = array(
                    $a => array(0 => array())
                );
                ',
                '<?php
                $arr = array(
                    $a =>
                    array(0 =>
                    array())
                );
                ',
            ),
            array(
                '<?php
                $a = array(
                    \'aaaaaa\' =>    \'b\',
                    \'c\' =>
                    \'d\',
                    \'eeeeee\' =>    \'f\',
                );',
                '<?php
                $a = array(
                    \'aaaaaa\' =>    \'b\',
                    \'c\' =>
                    \'d\',
                    \'eeeeee\' =>    \'f\',
                );',
            ),
            array(
                '<?php
                $a = array(
                    "aaaaaa" =>    array(),
                    "c" => array(),
                    "eeeeee" =>    array(),
                );',
                '<?php
                $a = array(
                    "aaaaaa" =>    array(),
                    "c" =>
                    array(),
                    "eeeeee" =>    array(),
                );',
            ),
        );
    }
}
