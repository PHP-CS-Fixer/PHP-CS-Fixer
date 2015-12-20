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

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class SpacesAfterSemicolonFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
                    test();
                    $a; // test
                ',
            ),
            array(
                '<?php test();     ',
            ),
            array(
                '<?php
                    test();     // test
                ',
            ),
            array(
                '<?php test();       /* */ //',
            ),
            array(
                '<?php
                    test(); $a = 4;
                ',
                '<?php
                    test();     $a = 4;
                ',
            ),
            array(
                '<?php
                    test(); $b = 7;
                ',
                '<?php
                    test();$b = 7;
                ',
            ),
            array(
                '<?php
                    for (; ; ) {
                    }
                ',
                '<?php
                    for (;;) {
                    }
                ',
            ),
            array(
                '<?php
                    for (; ; ++$u1) {
                    }
                ',
                '<?php
                    for (;;++$u1) {
                    }
                ',
            ),
            array(
                '<?php
                    for (; $u2 < 0; ) {
                    }
                ',
                '<?php
                    for (;$u2 < 0;) {
                    }
                ',
            ),
            array(
                '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
                '<?php
                    for (;$u3 < 3;++$u3) {
                    }
                ',
            ),
            array(
                '<?php
                    for ($u4 = 0; ; ) {
                    }
                ',
                '<?php
                    for ($u4 = 0;;) {
                    }
                ',
            ),
            array(
                '<?php
                    for ($u5 = 0; ; ++$u5) {
                    }
                ',
                '<?php
                    for ($u5 = 0;;++$u5) {
                    }
                ',
            ),
            array(
                '<?php
                    for ($u6 = 0; $u6 < 6; ) {
                    }
                ',
                '<?php
                    for ($u6 = 0;$u6 < 6;) {
                    }
                ',
            ),
            array(
                '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
                '<?php
                    for ($u7 = 0;$u7 < 7;++$u7) {
                    }
                ',
            ),
            array(
                '<?php
                    for (; ; ) {
                    }
                ',
                '<?php
                    for (;    ;    ) {
                    }
                ',
            ),
            array(
                '<?php
                    for (; ; ++$u1) {
                    }
                ',
                '<?php
                    for (;    ;    ++$u1) {
                    }
                ',
            ),
            array(
                '<?php
                    for (; $u2 < 0; ) {
                    }
                ',
                '<?php
                    for (;    $u2 < 0;    ) {
                    }
                ',
            ),
            array(
                '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
                '<?php
                    for (;    $u3 < 3;    ++$u3) {
                    }
                ',
            ),
            array(
                '<?php
                    for ($u4 = 0; ; ) {
                    }
                ',
                '<?php
                    for ($u4 = 0;    ;    ) {
                    }
                ',
            ),
            array(
                '<?php
                    for ($u5 = 0; ; ++$u5) {
                    }
                ',
                '<?php
                    for ($u5 = 0;    ;    ++$u5) {
                    }
                ',
            ),
            array(
                '<?php
                    for ($u6 = 0; $u6 < 6; ) {
                    }
                ',
                '<?php
                    for ($u6 = 0;    $u6 < 6;    ) {
                    }
                ',
            ),
            array(
                '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
                '<?php
                    for ($u7 = 0;    $u7 < 7;    ++$u7) {
                    }
                ',
            ),
        );
    }
}
