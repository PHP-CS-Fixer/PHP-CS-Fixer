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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer
 */
final class NoMultilineWhitespaceAroundDoubleArrowFixerTest extends AbstractFixerTestCase
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
        return array(
            array(
                '<?php
    $arr = array(
        $a => array(1),
        $a => array(0 => array())
    );',
                '<?php
    $arr = array(
        $a =>
            array(1),
        $a =>
            array(0 =>
            array())
    );',
            ),
            array(
                '<?php
    $a = array(
        "aaaaaa"    =>    "b",
        "c" => "d",
        "eeeeee" =>    array(),
        "ggg" => array(),
        "hh"      => [],
    );',
                '<?php
    $a = array(
        "aaaaaa"    =>    "b",
        "c"
            =>
                "d",
        "eeeeee" =>    array(),
        "ggg" =>
            array(),
        "hh"      =>
            [],
    );',
            ),
            array(
                '<?php
    $hello = array(
        "foo" =>
        // hello there
        "value",
        "hi"  =>
        /*
         * Description.
         */1,
        "ha"  =>
        /**
         * Description.
         */
        array()
    );',
            ),
        );
    }
}
