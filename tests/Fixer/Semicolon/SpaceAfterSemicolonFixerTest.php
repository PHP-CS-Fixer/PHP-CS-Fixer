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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer
 */
final class SpaceAfterSemicolonFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFixWithSpacesInEmptyForExpressions(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'remove_in_empty_for_expressions' => false,
        ]);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
                    test1();
                    $a; // test
                ',
        ];

        yield [
            '<?php test2();',
        ];

        yield [
            '<?php test3(); ',
        ];

        yield [
            '<?php test4();   ',
        ];

        yield [
            '<?php
                    test5();     // test
                ',
        ];

        yield [
            '<?php test6();       /* */ //',
        ];

        yield [
            '<?php test7a(); /* */',
            '<?php test7a();/* */',
        ];

        yield [
            '<?php test7b(); /* *//**/',
            '<?php test7b();/* *//**/',
        ];

        yield [
            '<?php
                    test8(); $a = 4;
                ',
            '<?php
                    test8();     $a = 4;
                ',
        ];

        yield [
            '<?php
                    test9(); $b = 7;
                ',
            '<?php
                    test9();$b = 7;
                ',
        ];

        yield [
            '<?php
                    for (; ;) {
                    }
                ',
            '<?php
                    for (;;) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; ; ++$u1) {
                    }
                ',
            '<?php
                    for (;;++$u1) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; $u2 < 0;) {
                    }
                ',
            '<?php
                    for (;$u2 < 0;) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
            '<?php
                    for (;$u3 < 3;++$u3) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u4 = 0; ;) {
                    }
                ',
            '<?php
                    for ($u4 = 0;;) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u5 = 0; ; ++$u5) {
                    }
                ',
            '<?php
                    for ($u5 = 0;;++$u5) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u6 = 0; $u6 < 6;) {
                    }
                ',
            '<?php
                    for ($u6 = 0;$u6 < 6;) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
            '<?php
                    for ($u7 = 0;$u7 < 7;++$u7) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; ;    ) {
                    }
                ',
            '<?php
                    for (;    ;    ) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; ; ++$u1) {
                    }
                ',
            '<?php
                    for (;    ;    ++$u1) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; $u2 < 0;    ) {
                    }
                ',
            '<?php
                    for (;    $u2 < 0;    ) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
            '<?php
                    for (;    $u3 < 3;    ++$u3) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($ui4 = 0; ;    ) {
                    }
                ',
            '<?php
                    for ($ui4 = 0;    ;    ) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u5 = 0; ; ++$u5) {
                    }
                ',
            '<?php
                    for ($u5 = 0;    ;    ++$u5) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u6 = 0; $u6 < 6;    ) {
                    }
                ',
            '<?php
                    for ($u6 = 0;    $u6 < 6;    ) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
            '<?php
                    for ($u7 = 0;    $u7 < 7;    ++$u7) {
                    }
                ',
        ];

        yield [
            '<?php if ($a):?>
                    1
                <?php endif; ?>
                <?php if ($b):?>
                    2
                <?php endif; ?>
                <?php if ($c):?>
                    3
                <?php endif; ?>',
            '<?php if ($a):?>
                    1
                <?php endif;?>
                <?php if ($b):?>
                    2
                <?php endif;?>
                <?php if ($c):?>
                    3
                <?php endif;?>',
        ];

        yield [
            '<?php echo 1; ; ; ; ; ; ; ; ;',
            '<?php echo 1;;;;;;;;;',
        ];
    }

    /**
     * @dataProvider provideFixWithoutSpacesInEmptyForExpressionsCases
     */
    public function testFixWithoutSpacesInEmptyForExpressions(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'remove_in_empty_for_expressions' => true,
        ]);
        $this->doTest($expected, $input);
    }

    public static function provideFixWithoutSpacesInEmptyForExpressionsCases(): iterable
    {
        yield [
            '<?php
                    test1();
                    $a; // test
                ',
        ];

        yield [
            '<?php test2();',
        ];

        yield [
            '<?php test3(); ',
        ];

        yield [
            '<?php test4();   ',
        ];

        yield [
            '<?php
                    test5();     // test
                ',
        ];

        yield [
            '<?php test6();       /* */ //',
        ];

        yield [
            '<?php test7a(); /* */',
            '<?php test7a();/* */',
        ];

        yield [
            '<?php test7b(); /* *//**/',
            '<?php test7b();/* *//**/',
        ];

        yield [
            '<?php
                    test8(); $a = 4;
                ',
            '<?php
                    test8();     $a = 4;
                ',
        ];

        yield [
            '<?php
                    test9(); $b = 7;
                ',
            '<?php
                    test9();$b = 7;
                ',
        ];

        yield [
            '<?php
                    for (;;) {
                    }
                ',
            '<?php
                    for (; ;) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (;; ++$u1) {
                    }
                ',
            '<?php
                    for (;;++$u1) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; $u2 < 0;) {
                    }
                ',
            '<?php
                    for (;$u2 < 0;) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
            '<?php
                    for (;$u3 < 3;++$u3) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u4 = 0;;) {
                    }
                ',
            '<?php
                    for ($u4 = 0; ;) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u5 = 0;; ++$u5) {
                    }
                ',
            '<?php
                    for ($u5 = 0;;++$u5) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u6 = 0; $u6 < 6;) {
                    }
                ',
            '<?php
                    for ($u6 = 0;$u6 < 6;) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
            '<?php
                    for ($u7 = 0;$u7 < 7;++$u7) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (;;) {
                    }
                ',
            '<?php
                    for (;    ;    ) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (;; ++$u1) {
                    }
                ',
            '<?php
                    for (;    ;    ++$u1) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; $u2 < 0;) {
                    }
                ',
            '<?php
                    for (;    $u2 < 0;    ) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (; $u3 < 3; ++$u3) {
                    }
                ',
            '<?php
                    for (;    $u3 < 3;    ++$u3) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($ui4 = 0;;) {
                    }
                ',
            '<?php
                    for ($ui4 = 0;    ;    ) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u5 = 0;; ++$u5) {
                    }
                ',
            '<?php
                    for ($u5 = 0;    ;    ++$u5) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u6 = 0; $u6 < 6;) {
                    }
                ',
            '<?php
                    for ($u6 = 0;    $u6 < 6;    ) {
                    }
                ',
        ];

        yield [
            '<?php
                    for ($u7 = 0; $u7 < 7; ++$u7) {
                    }
                ',
            '<?php
                    for ($u7 = 0;    $u7 < 7;    ++$u7) {
                    }
                ',
        ];

        yield [
            '<?php
                    for (
                        $u7 = 0;
                        ;
                        ++$u7
                    ) {
                    }
                ',
        ];

        yield [
            '<?php for ( /* foo */ ; /* bar */ ; /* baz */ ) { }',
        ];
    }

    public function testHaltCompiler(): void
    {
        $this->doTest('<?php
            __HALT_COMPILER();
        ');
    }
}
