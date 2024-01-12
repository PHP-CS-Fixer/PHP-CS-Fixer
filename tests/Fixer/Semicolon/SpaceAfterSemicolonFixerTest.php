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
            <<<'EOD'
                <?php
                                    test1();
                                    $a; // test
                EOD."\n                ",
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
            <<<'EOD'
                <?php
                                    test5();     // test
                EOD."\n                ",
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
            <<<'EOD'
                <?php
                                    test8(); $a = 4;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    test8();     $a = 4;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    test9(); $b = 7;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    test9();$b = 7;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; ;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;;) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; ; ++$u1) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;;++$u1) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; $u2 < 0;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;$u2 < 0;) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; $u3 < 3; ++$u3) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;$u3 < 3;++$u3) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u4 = 0; ;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u4 = 0;;) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u5 = 0; ; ++$u5) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u5 = 0;;++$u5) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u6 = 0; $u6 < 6;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u6 = 0;$u6 < 6;) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u7 = 0; $u7 < 7; ++$u7) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u7 = 0;$u7 < 7;++$u7) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; ;    ) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;    ;    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; ; ++$u1) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;    ;    ++$u1) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; $u2 < 0;    ) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;    $u2 < 0;    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; $u3 < 3; ++$u3) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;    $u3 < 3;    ++$u3) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($ui4 = 0; ;    ) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($ui4 = 0;    ;    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u5 = 0; ; ++$u5) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u5 = 0;    ;    ++$u5) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u6 = 0; $u6 < 6;    ) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u6 = 0;    $u6 < 6;    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u7 = 0; $u7 < 7; ++$u7) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u7 = 0;    $u7 < 7;    ++$u7) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php if ($a):?>
                                    1
                                <?php endif; ?>
                                <?php if ($b):?>
                                    2
                                <?php endif; ?>
                                <?php if ($c):?>
                                    3
                                <?php endif; ?>
                EOD,
            <<<'EOD'
                <?php if ($a):?>
                                    1
                                <?php endif;?>
                                <?php if ($b):?>
                                    2
                                <?php endif;?>
                                <?php if ($c):?>
                                    3
                                <?php endif;?>
                EOD,
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
            <<<'EOD'
                <?php
                                    test1();
                                    $a; // test
                EOD."\n                ",
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
            <<<'EOD'
                <?php
                                    test5();     // test
                EOD."\n                ",
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
            <<<'EOD'
                <?php
                                    test8(); $a = 4;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    test8();     $a = 4;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    test9(); $b = 7;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    test9();$b = 7;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (;;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (; ;) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (;; ++$u1) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;;++$u1) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; $u2 < 0;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;$u2 < 0;) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; $u3 < 3; ++$u3) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;$u3 < 3;++$u3) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u4 = 0;;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u4 = 0; ;) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u5 = 0;; ++$u5) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u5 = 0;;++$u5) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u6 = 0; $u6 < 6;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u6 = 0;$u6 < 6;) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u7 = 0; $u7 < 7; ++$u7) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u7 = 0;$u7 < 7;++$u7) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (;;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;    ;    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (;; ++$u1) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;    ;    ++$u1) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; $u2 < 0;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;    $u2 < 0;    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (; $u3 < 3; ++$u3) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for (;    $u3 < 3;    ++$u3) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($ui4 = 0;;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($ui4 = 0;    ;    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u5 = 0;; ++$u5) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u5 = 0;    ;    ++$u5) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u6 = 0; $u6 < 6;) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u6 = 0;    $u6 < 6;    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for ($u7 = 0; $u7 < 7; ++$u7) {
                                    }
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    for ($u7 = 0;    $u7 < 7;    ++$u7) {
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    for (
                                        $u7 = 0;
                                        ;
                                        ++$u7
                                    ) {
                                    }
                EOD."\n                ",
        ];

        yield [
            '<?php for ( /* foo */ ; /* bar */ ; /* baz */ ) { }',
        ];
    }

    public function testHaltCompiler(): void
    {
        $this->doTest(<<<'EOD'
            <?php
                        __HALT_COMPILER();
            EOD."\n        ");
    }
}
