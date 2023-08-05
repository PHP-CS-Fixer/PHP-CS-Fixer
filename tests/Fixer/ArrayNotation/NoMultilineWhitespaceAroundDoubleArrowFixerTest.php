<?php

declare(strict_types=1);

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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer
 */
final class NoMultilineWhitespaceAroundDoubleArrowFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
                    $fn = fn() => null;',
            '<?php
                    $fn = fn()
                        =>
                            null;',
        ];

        yield [
            '<?php
                    $foo = [
                        1 /* foo */ => $one,
                        2 => $two
                    ];',
            '<?php
                    $foo = [
                        1 /* foo */
                            =>
                                $one,
                        2
                            =>
                                $two
                    ];',
        ];

        yield [
            '<?php
                    $foo = [
                        1 // foo
                            => $one,
                        2 => $two,
                    ];',
            '<?php
                    $foo = [
                        1 // foo
                            =>
                                $one,
                        2
                            =>
                                $two,
                    ];',
        ];
    }
}
