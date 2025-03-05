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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer>
 *
 * @author Gregor Harlan <gharlan@web.de>
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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOF'
                <?php $a = <<<'TEST'
                Foo $bar \n
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = <<<'TEST'
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = <<<TEST
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = <<<'TEST'
                Foo \\ $bar \n
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = <<<TEST
                Foo \\\\ \$bar \\n
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = <<<'TEST'
                Foo
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = <<<"TEST"
                Foo
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = <<<TEST
                Foo $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = <<<TEST
                Foo \\$bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = <<<TEST
                Foo \n $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = <<<TEST
                Foo \x00 $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php
                $html = <<<   'HTML'
                a
                HTML;

                EOF,
            <<<'EOF'
                <?php
                $html = <<<   HTML
                a
                HTML;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = <<<           'TEST'
                Foo
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = <<<           "TEST"
                Foo
                TEST;

                EOF,
        ];

        yield [
            <<<EOF
                <?php echo <<<'TEST'\r\nFoo\r\nTEST;

                EOF,
            <<<EOF
                <?php echo <<<TEST\r\nFoo\r\nTEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<'TEST'
                Foo $bar \n
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<'TEST'
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = b<<<TEST
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<'TEST'
                Foo \\ $bar \n
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = b<<<TEST
                Foo \\\\ \$bar \\n
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<'TEST'
                Foo
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = b<<<"TEST"
                Foo
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<TEST
                Foo $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<TEST
                Foo \\$bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<TEST
                Foo \n $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<TEST
                Foo \x00 $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php
                $html = b<<<   'HTML'
                a
                HTML;

                EOF,
            <<<'EOF'
                <?php
                $html = b<<<   HTML
                a
                HTML;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = b<<<           'TEST'
                Foo
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = b<<<           "TEST"
                Foo
                TEST;

                EOF,
        ];

        yield [
            <<<EOF
                <?php echo b<<<'TEST'\r\nFoo\r\nTEST;

                EOF,
            <<<EOF
                <?php echo b<<<TEST\r\nFoo\r\nTEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<'TEST'
                Foo $bar \n
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<'TEST'
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = B<<<TEST
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<'TEST'
                Foo \\ $bar \n
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = B<<<TEST
                Foo \\\\ \$bar \\n
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<'TEST'
                Foo
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = B<<<"TEST"
                Foo
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<TEST
                Foo $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<TEST
                Foo \\$bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<TEST
                Foo \n $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<TEST
                Foo \x00 $bar
                TEST;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php
                $html = B<<<   'HTML'
                a
                HTML;

                EOF,
            <<<'EOF'
                <?php
                $html = B<<<   HTML
                a
                HTML;

                EOF,
        ];

        yield [
            <<<'EOF'
                <?php $a = B<<<           'TEST'
                Foo
                TEST;

                EOF,
            <<<'EOF'
                <?php $a = B<<<           "TEST"
                Foo
                TEST;

                EOF,
        ];

        yield [
            <<<EOF
                <?php echo B<<<'TEST'\r\nFoo\r\nTEST;

                EOF,
            <<<EOF
                <?php echo B<<<TEST\r\nFoo\r\nTEST;

                EOF,
        ];
    }
}
