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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer
 */
final class SpaceAfterSemicolonFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
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
                    test1();
                    $a; // test
                ',
            ),
            array(
                '<?php test2();',
            ),
            array(
                '<?php test3(); ',
            ),
            array(
                '<?php test4();   ',
            ),
            array(
                '<?php
                    test5();     // test
                ',
            ),
            array(
                '<?php test6();       /* */ //',
            ),
            array(
                '<?php test7a(); /* */',
                '<?php test7a();/* */',
            ),
            array(
                '<?php test7b(); /* *//**/',
                '<?php test7b();/* *//**/',
            ),
            array(
                '<?php
                    test8(); $a = 4;
                ',
                '<?php
                    test8();     $a = 4;
                ',
            ),
            array(
                '<?php
                    test9(); $b = 7;
                ',
                '<?php
                    test9();$b = 7;
                ',
            ),
            array(
                '<?php
                    for (; ;) {
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
                    for (; $u2 < 0;) {
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
                    for ($u4 = 0; ;) {
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
                    for ($u6 = 0; $u6 < 6;) {
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
                    for (; ;    ) {
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
                    for (; $u2 < 0;    ) {
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
                    for ($ui4 = 0; ;    ) {
                    }
                ',
                '<?php
                    for ($ui4 = 0;    ;    ) {
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
                    for ($u6 = 0; $u6 < 6;    ) {
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

    /**
     * @requires PHP 5.4
     */
    public function testHaltCompiler()
    {
        $this->doTest('<?php
            __HALT_COMPILER();
        ');
    }
}
