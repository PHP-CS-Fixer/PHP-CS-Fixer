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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

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
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFixWithSpacesInEmptyForExpressions($expected, $input = null)
    {
        $this->fixer->configure([
            'remove_in_empty_for_expressions' => false,
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
                    test1();
                    $a; // test
                ',
            ],
            [
                '<?php test2();',
            ],
            [
                '<?php test3(); ',
            ],
            [
                '<?php test4();   ',
            ],
            [
                '<?php
                    test5();     // test
                ',
            ],
            [
                '<?php test6();       /* */ //',
            ],
            [
                '<?php test7a(); /* */',
                '<?php test7a();/* */',
            ],
            [
                '<?php test7b(); /* *//**/',
                '<?php test7b();/* *//**/',
            ],
            [
                '<?php
                    test8(); $a = 4;
                ',
                '<?php
                    test8();     $a = 4;
                ',
            ],
            [
                '<?php
                    test9(); $b = 7;
                ',
                '<?php
                    test9();$b = 7;
                ',
            ],
            [
                '<?php
                    for (; ;) {
                    }
                ',
                '<?php
                    for (;;) {
                    }
                ',
            ],
            [
                '<?php
                    for (; ; ++$u1) {
                    }
                ',
                '<?php
                    for (;;++$u1) {
                    }
                ',
            ],
            [
                '<?php
                    for (; $u2 < 0;) {
                    }
                ',
                '<?php
                    for (;$u2 < 0;) {
                    }
                ',
            ],
            [
                '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
                '<?php
                    for (;$u3 < 3;++$u3) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u4 = 0; ;) {
                    }
                ',
                '<?php
                    for ($u4 = 0;;) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u5 = 0; ; ++$u5) {
                    }
                ',
                '<?php
                    for ($u5 = 0;;++$u5) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u6 = 0; $u6 < 6;) {
                    }
                ',
                '<?php
                    for ($u6 = 0;$u6 < 6;) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
                '<?php
                    for ($u7 = 0;$u7 < 7;++$u7) {
                    }
                ',
            ],
            [
                '<?php
                    for (; ;    ) {
                    }
                ',
                '<?php
                    for (;    ;    ) {
                    }
                ',
            ],
            [
                '<?php
                    for (; ; ++$u1) {
                    }
                ',
                '<?php
                    for (;    ;    ++$u1) {
                    }
                ',
            ],
            [
                '<?php
                    for (; $u2 < 0;    ) {
                    }
                ',
                '<?php
                    for (;    $u2 < 0;    ) {
                    }
                ',
            ],
            [
                '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
                '<?php
                    for (;    $u3 < 3;    ++$u3) {
                    }
                ',
            ],
            [
                '<?php
                    for ($ui4 = 0; ;    ) {
                    }
                ',
                '<?php
                    for ($ui4 = 0;    ;    ) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u5 = 0; ; ++$u5) {
                    }
                ',
                '<?php
                    for ($u5 = 0;    ;    ++$u5) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u6 = 0; $u6 < 6;    ) {
                    }
                ',
                '<?php
                    for ($u6 = 0;    $u6 < 6;    ) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
                '<?php
                    for ($u7 = 0;    $u7 < 7;    ++$u7) {
                    }
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithoutSpacesInEmptyForExpressionsCases
     */
    public function testFixWithoutSpacesInEmptyForExpressions($expected, $input = null)
    {
        $this->fixer->configure([
            'remove_in_empty_for_expressions' => true,
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFixWithoutSpacesInEmptyForExpressionsCases()
    {
        return [
            [
                '<?php
                    test1();
                    $a; // test
                ',
            ],
            [
                '<?php test2();',
            ],
            [
                '<?php test3(); ',
            ],
            [
                '<?php test4();   ',
            ],
            [
                '<?php
                    test5();     // test
                ',
            ],
            [
                '<?php test6();       /* */ //',
            ],
            [
                '<?php test7a(); /* */',
                '<?php test7a();/* */',
            ],
            [
                '<?php test7b(); /* *//**/',
                '<?php test7b();/* *//**/',
            ],
            [
                '<?php
                    test8(); $a = 4;
                ',
                '<?php
                    test8();     $a = 4;
                ',
            ],
            [
                '<?php
                    test9(); $b = 7;
                ',
                '<?php
                    test9();$b = 7;
                ',
            ],
            [
                '<?php
                    for (;;) {
                    }
                ',
                '<?php
                    for (; ;) {
                    }
                ',
            ],
            [
                '<?php
                    for (;; ++$u1) {
                    }
                ',
                '<?php
                    for (;;++$u1) {
                    }
                ',
            ],
            [
                '<?php
                    for (; $u2 < 0;) {
                    }
                ',
                '<?php
                    for (;$u2 < 0;) {
                    }
                ',
            ],
            [
                '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
                '<?php
                    for (;$u3 < 3;++$u3) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u4 = 0;;) {
                    }
                ',
                '<?php
                    for ($u4 = 0; ;) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u5 = 0;; ++$u5) {
                    }
                ',
                '<?php
                    for ($u5 = 0;;++$u5) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u6 = 0; $u6 < 6;) {
                    }
                ',
                '<?php
                    for ($u6 = 0;$u6 < 6;) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
                '<?php
                    for ($u7 = 0;$u7 < 7;++$u7) {
                    }
                ',
            ],
            [
                '<?php
                    for (;;) {
                    }
                ',
                '<?php
                    for (;    ;    ) {
                    }
                ',
            ],
            [
                '<?php
                    for (;; ++$u1) {
                    }
                ',
                '<?php
                    for (;    ;    ++$u1) {
                    }
                ',
            ],
            [
                '<?php
                    for (; $u2 < 0;) {
                    }
                ',
                '<?php
                    for (;    $u2 < 0;    ) {
                    }
                ',
            ],
            [
                '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
                '<?php
                    for (;    $u3 < 3;    ++$u3) {
                    }
                ',
            ],
            [
                '<?php
                    for ($ui4 = 0;;) {
                    }
                ',
                '<?php
                    for ($ui4 = 0;    ;    ) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u5 = 0;; ++$u5) {
                    }
                ',
                '<?php
                    for ($u5 = 0;    ;    ++$u5) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u6 = 0; $u6 < 6;) {
                    }
                ',
                '<?php
                    for ($u6 = 0;    $u6 < 6;    ) {
                    }
                ',
            ],
            [
                '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
                '<?php
                    for ($u7 = 0;    $u7 < 7;    ++$u7) {
                    }
                ',
            ],
            [
                '<?php
                    for (
                        $u7 = 0;
                        ;
                        ++$u7
                    ) {
                    }
                ',
            ],
            [
                '<?php for ( /* foo */ ; /* bar */ ; /* baz */ ) { }',
            ],
        ];
    }

    public function testHaltCompiler()
    {
        $this->doTest('<?php
            __HALT_COMPILER();
        ');
    }
}
