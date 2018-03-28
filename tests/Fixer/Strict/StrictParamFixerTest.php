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
        return [
            [
                '<?php
    in_array(1, $a, true);
    in_array(1, $a, false);
    in_array(1, $a, $useStrict);',
            ],
            [
                '<?php
    in_array(1, $a, true);',
                '<?php
    in_array(1, $a);',
            ],
            [
                '<?php
    in_array(1, foo(), true);',
                '<?php
    in_array(1, foo());',
            ],
            [
                '<?php
    in_array(1, array(1, 2, 3), true);',
                '<?php
    in_array(1, array(1, 2, 3));',
            ],
            [
                '<?php
    in_array(1, [1, 2, 3], true);',
                '<?php
    in_array(1, [1, 2, 3]);',
            ],
            [
                '<?php
    in_array(in_array(1, [1, in_array(1, [1, 2, 3], true) ? 21 : 22, 3], true) ? 111 : 222, [1, in_array(1, [1, 2, 3], true) ? 21 : 22, 3], true);',
                '<?php
    in_array(in_array(1, [1, in_array(1, [1, 2, 3]) ? 21 : 22, 3]) ? 111 : 222, [1, in_array(1, [1, 2, 3]) ? 21 : 22, 3]);',
            ],
            [
                '<?php
    base64_decode($foo, true);
    base64_decode($foo, false);
    base64_decode($foo, $useStrict);',
            ],
            [
                '<?php
    base64_decode($foo, true);',
                '<?php
    base64_decode($foo);',
            ],
            [
                '<?php
    array_search($foo, $bar, true);
    array_search($foo, $bar, false);
    array_search($foo, $bar, $useStrict);',
            ],
            [
                '<?php
    array_search($foo, $bar, true);',
                '<?php
    array_search($foo, $bar);',
            ],
            [
                '<?php
    array_keys($foo);
    array_keys($foo, $bar, true);
    array_keys($foo, $bar, false);
    array_keys($foo, $bar, $useStrict);',
            ],
            [
                '<?php
    array_keys($foo, $bar, true);',
                '<?php
    array_keys($foo, $bar);',
            ],
            [
                '<?php
    mb_detect_encoding($foo, $bar, true);
    mb_detect_encoding($foo, $bar, false);
    mb_detect_encoding($foo, $bar, $useStrict);',
            ],
            [
                '<?php
    mb_detect_encoding($foo, mb_detect_order(), true);',
                '<?php
    mb_detect_encoding($foo);',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix56Cases
     * @requires PHP 5.6
     */
    public function testFix56($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix56Cases()
    {
        return [
            [
                '<?php
    use function in_array;

    class Foo
    {
        public function __construct($foo, $bar) {}
    }',
            ],
        ];
    }
}
