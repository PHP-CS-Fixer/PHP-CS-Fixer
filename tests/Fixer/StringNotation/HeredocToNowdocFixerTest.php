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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer
 */
final class HeredocToNowdocFixerTest extends AbstractFixerTestCase
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
            <<<'EOD'
                <?php $a = <<<'TEST'
                Foo $bar \n
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = <<<'TEST'
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = <<<TEST
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = <<<'TEST'
                Foo \\ $bar \n
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = <<<TEST
                Foo \\\\ \$bar \\n
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = <<<'TEST'
                Foo
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = <<<"TEST"
                Foo
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = <<<TEST
                Foo $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = <<<TEST
                Foo \\$bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = <<<TEST
                Foo \n $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = <<<TEST
                Foo \x00 $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php
                $html = <<<   'HTML'
                a
                HTML;

                EOD,
            <<<'EOD'
                <?php
                $html = <<<   HTML
                a
                HTML;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = <<<           'TEST'
                Foo
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = <<<           "TEST"
                Foo
                TEST;

                EOD
        ];

        yield [
            <<<EOD
                <?php echo <<<'TEST'\r\nFoo\r\nTEST;

                EOD,
            <<<EOD
                <?php echo <<<TEST\r\nFoo\r\nTEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<'TEST'
                Foo $bar \n
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<'TEST'
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = b<<<TEST
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<'TEST'
                Foo \\ $bar \n
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = b<<<TEST
                Foo \\\\ \$bar \\n
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<'TEST'
                Foo
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = b<<<"TEST"
                Foo
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<TEST
                Foo $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<TEST
                Foo \\$bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<TEST
                Foo \n $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<TEST
                Foo \x00 $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php
                $html = b<<<   'HTML'
                a
                HTML;

                EOD,
            <<<'EOD'
                <?php
                $html = b<<<   HTML
                a
                HTML;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = b<<<           'TEST'
                Foo
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = b<<<           "TEST"
                Foo
                TEST;

                EOD
        ];

        yield [
            <<<EOD
                <?php echo b<<<'TEST'\r\nFoo\r\nTEST;

                EOD,
            <<<EOD
                <?php echo b<<<TEST\r\nFoo\r\nTEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<'TEST'
                Foo $bar \n
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<'TEST'
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = B<<<TEST
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<'TEST'
                Foo \\ $bar \n
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = B<<<TEST
                Foo \\\\ \$bar \\n
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<'TEST'
                Foo
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = B<<<"TEST"
                Foo
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<TEST
                Foo $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<TEST
                Foo \\$bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<TEST
                Foo \n $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<TEST
                Foo \x00 $bar
                TEST;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php
                $html = B<<<   'HTML'
                a
                HTML;

                EOD,
            <<<'EOD'
                <?php
                $html = B<<<   HTML
                a
                HTML;

                EOD
        ];

        yield [
            <<<'EOD'
                <?php $a = B<<<           'TEST'
                Foo
                TEST;

                EOD,
            <<<'EOD'
                <?php $a = B<<<           "TEST"
                Foo
                TEST;

                EOD
        ];

        yield [
            <<<EOD
                <?php echo B<<<'TEST'\r\nFoo\r\nTEST;

                EOD,
            <<<EOD
                <?php echo B<<<TEST\r\nFoo\r\nTEST;

                EOD
        ];
    }
}
