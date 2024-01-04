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
 * @covers \PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer
 */
final class SingleQuoteFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $a = \'\';',
            '<?php $a = "";',
        ];

        yield [
            '<?php $a = \'foo bar\';',
            '<?php $a = "foo bar";',
        ];

        yield [
            '<?php $a = b\'\';',
            '<?php $a = b"";',
        ];

        yield [
            '<?php $a = B\'\';',
            '<?php $a = B"";',
        ];

        yield [
            '<?php $a = \'foo bar\';',
            '<?php $a = "foo bar";',
        ];

        yield [
            '<?php $a = b\'foo bar\';',
            '<?php $a = b"foo bar";',
        ];

        yield [
            '<?php $a = B\'foo bar\';',
            '<?php $a = B"foo bar";',
        ];

        yield [
            '<?php $a = \'foo
                    bar\';',
            '<?php $a = "foo
                    bar";',
        ];

        yield [
            '<?php $a = \'foo\'.\'bar\'."$baz";',
            '<?php $a = \'foo\'."bar"."$baz";',
        ];

        yield [
            '<?php $a = \'foo "bar"\';',
            '<?php $a = "foo \"bar\"";',
        ];

        yield [
            <<<'EOD'
                <?php $a = '\\foo\\bar\\\\';
                EOD,
            <<<'EOD'
                <?php $a = "\\foo\\bar\\\\";
                EOD
        ];

        yield [
            '<?php $a = \'foo $bar7\';',
            '<?php $a = "foo \$bar7";',
        ];

        yield [
            '<?php $a = \'foo $(bar7)\';',
            '<?php $a = "foo \$(bar7)";',
        ];

        yield [
            '<?php $a = \'foo \\\\($bar8)\';',
            '<?php $a = "foo \\\\(\$bar8)";',
        ];

        yield ['<?php $a = "foo \\" \\$$bar";'];

        yield ['<?php $a = b"foo \\" \\$$bar";'];

        yield ['<?php $a = B"foo \\" \\$$bar";'];

        yield ['<?php $a = "foo \'bar\'";'];

        yield ['<?php $a = b"foo \'bar\'";'];

        yield ['<?php $a = B"foo \'bar\'";'];

        yield ['<?php $a = "foo $bar";'];

        yield ['<?php $a = b"foo $bar";'];

        yield ['<?php $a = B"foo $bar";'];

        yield ['<?php $a = "foo ${bar}";'];

        yield ['<?php $a = b"foo ${bar}";'];

        yield ['<?php $a = B"foo ${bar}";'];

        yield ['<?php $a = "foo\n bar";'];

        yield ['<?php $a = b"foo\n bar";'];

        yield ['<?php $a = B"foo\n bar";'];

        yield [
            <<<'EOD'
                <?php $a = "\\\n";
                EOD
        ];

        yield [
            '<?php $a = \'foo \\\'bar\\\'\';',
            '<?php $a = "foo \'bar\'";',
            ['strings_containing_single_quote_chars' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                // none
                $a = 'start \' end';
                // one escaped baskslash
                $b = 'start \\\' end';
                // two escaped baskslash
                $c = 'start \\\\\' end';
                EOD,
            <<<'EOD'
                <?php
                // none
                $a = "start ' end";
                // one escaped baskslash
                $b = "start \\' end";
                // two escaped baskslash
                $c = "start \\\\' end";
                EOD,
            ['strings_containing_single_quote_chars' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                // one unescaped backslash
                $a = "start \' end";
                // one escaped + one unescaped baskslash
                $b = "start \\\' end";
                EOD,
            null,
            ['strings_containing_single_quote_chars' => true],
        ];
    }
}
