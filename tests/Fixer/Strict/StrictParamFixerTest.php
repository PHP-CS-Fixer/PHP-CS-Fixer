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

namespace PhpCsFixer\Tests\Fixer\Strict;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Strict\StrictParamFixer
 */
final class StrictParamFixerTest extends AbstractFixerTestCase
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
    in_array(1, $a, true);
    in_array(1, $a, false);
    in_array(1, $a, $useStrict);',
            ),
            array(
                '<?php
    in_array(1, $a, true);',
                '<?php
    in_array(1, $a);',
            ),
            array(
                '<?php
    in_array(1, foo(), true);',
                '<?php
    in_array(1, foo());',
            ),
            array(
                '<?php
    in_array(1, array(1, 2, 3), true);',
                '<?php
    in_array(1, array(1, 2, 3));',
            ),
            array(
                '<?php
    in_array(1, [1, 2, 3], true);',
                '<?php
    in_array(1, [1, 2, 3]);',
            ),
            array(
                '<?php
    in_array(in_array(1, [1, in_array(1, [1, 2, 3], true) ? 21 : 22, 3], true) ? 111 : 222, [1, in_array(1, [1, 2, 3], true) ? 21 : 22, 3], true);',
                '<?php
    in_array(in_array(1, [1, in_array(1, [1, 2, 3]) ? 21 : 22, 3]) ? 111 : 222, [1, in_array(1, [1, 2, 3]) ? 21 : 22, 3]);',
            ),
            array(
                '<?php
    base64_decode($foo, true);
    base64_decode($foo, false);
    base64_decode($foo, $useStrict);',
            ),
            array(
                '<?php
    base64_decode($foo, true);',
                '<?php
    base64_decode($foo);',
            ),
            array(
                '<?php
    array_search($foo, $bar, true);
    array_search($foo, $bar, false);
    array_search($foo, $bar, $useStrict);',
            ),
            array(
                '<?php
    array_search($foo, $bar, true);',
                '<?php
    array_search($foo, $bar);',
            ),
            array(
                '<?php
    array_keys($foo);
    array_keys($foo, $bar, true);
    array_keys($foo, $bar, false);
    array_keys($foo, $bar, $useStrict);',
            ),
            array(
                '<?php
    array_keys($foo, $bar, true);',
                '<?php
    array_keys($foo, $bar);',
            ),
            array(
                '<?php
    mb_detect_encoding($foo, $bar, true);
    mb_detect_encoding($foo, $bar, false);
    mb_detect_encoding($foo, $bar, $useStrict);',
            ),
            array(
                '<?php
    mb_detect_encoding($foo, mb_detect_order(), true);',
                '<?php
    mb_detect_encoding($foo);',
            ),
        );
    }
}
