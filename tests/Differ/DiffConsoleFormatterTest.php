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

namespace PhpCsFixer\Tests\Differ;

use PhpCsFixer\Differ\DiffConsoleFormatter;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Differ\DiffConsoleFormatter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(DiffConsoleFormatter::class)]
final class DiffConsoleFormatterTest extends TestCase
{
    /**
     * @dataProvider provideDiffConsoleFormatterCases
     */
    #[DataProvider('provideDiffConsoleFormatterCases')]
    public function testDiffConsoleFormatter(string $expected, bool $isDecoratedOutput, string $template, string $diff, string $lineTemplate): void
    {
        $diffFormatter = new DiffConsoleFormatter($isDecoratedOutput, $template);

        self::assertSame(
            str_replace(\PHP_EOL, "\n", $expected),
            str_replace(\PHP_EOL, "\n", $diffFormatter->format($diff, $lineTemplate)),
        );
    }

    /**
     * @return iterable<int, array{string, bool, string, string, string}>
     */
    public static function provideDiffConsoleFormatterCases(): iterable
    {
        yield [
            \sprintf(
                '<comment>   ---------- begin diff ----------</comment>
   '.'
   <fg=cyan>%s</fg=cyan>
    no change
   <fg=red>%s</fg=red>
   <fg=green>%s</fg=green>
   <fg=green>%s</fg=green>
   '.'
<comment>   ----------- end diff -----------</comment>',
                OutputFormatter::escape('@@ -12,51 +12,151 @@'),
                OutputFormatter::escape('-/**\\'),
                OutputFormatter::escape('+/*\\'),
                OutputFormatter::escape('+A'),
            ),
            true,
            \sprintf(
                '<comment>   ---------- begin diff ----------</comment>%s%%s%s<comment>   ----------- end diff -----------</comment>',
                \PHP_EOL,
                \PHP_EOL,
            ),
            '
@@ -12,51 +12,151 @@
 no change
-/**\
+/*\
+A
',
            '   %s',
        ];

        yield [
            '[start]
| '.'
| @@ -12,51 +12,151 @@
|  no change
|  '.'
| -/**\
| +/*\
| +A
| '.'
[end]',
            false,
            \sprintf('[start]%s%%s%s[end]', \PHP_EOL, \PHP_EOL),
            '
@@ -12,51 +12,151 @@
 no change
 '.'
-/**\
+/*\
+A
',
            '| %s',
        ];

        yield [
            (string) mb_convert_encoding("<fg=red>--- Original</fg=red>\n<fg=green>+ausgefüllt</fg=green>", 'ISO-8859-1'),
            true,
            '%s',
            (string) mb_convert_encoding("--- Original\n+ausgefüllt", 'ISO-8859-1'),
            '%s',
        ];

        yield [
            (string) mb_convert_encoding("<fg=red>--- Original</fg=red>\n<fg=green>+++ New</fg=green>\n<fg=cyan>@@ @@</fg=cyan>\n<fg=red>-ausgefüllt</fg=red>", 'ISO-8859-1'),
            true,
            '%s',
            (string) mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
            '%s',
        ];

        yield [
            (string) mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
            false,
            '%s',
            (string) mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
            '%s',
        ];

        // CSI sequences (colours, cursor movement) are stripped even when decorated
        yield [
            "<fg=green>+hello world</fg=green>",
            true,
            '%s',
            "+hello \x1B[31mworld\x1B[0m",
            '%s',
        ];

        yield [
            '+hello world',
            false,
            '%s',
            "+hello \x1B[31mworld\x1B[0m",
            '%s',
        ];

        // OSC terminated by BEL (window title) is stripped
        yield [
            '<fg=red>-evil line</fg=red>',
            true,
            '%s',
            "-evil \x1B]0;PWNED\x07line",
            '%s',
        ];

        yield [
            '-evil line',
            false,
            '%s',
            "-evil \x1B]0;PWNED\x07line",
            '%s',
        ];

        // OSC terminated by ST (ESC \) – e.g. hyperlinks – is stripped
        yield [
            '<fg=green>+foo bar</fg=green>',
            true,
            '%s',
            "+foo \x1B]8;;https://example.com\x1B\\bar",
            '%s',
        ];

        // two-byte escapes (e.g. reverse index ESC M) are stripped – pattern covers ESC [@-Z \ _
        yield [
            ' context line',
            true,
            '%s',
            " context \x1BMline",
            '%s',
        ];

        // cursor movement / erase CSI sequences are stripped
        yield [
            '<fg=cyan>@@ -1 +1 @@</fg=cyan>',
            true,
            '%s',
            "@@ \x1B[2J\x1B[999;999H-1 +1 @@",
            '%s',
        ];

        // multiple mixed sequences on one line are all stripped
        yield [
            '<fg=green>+abc</fg=green>',
            true,
            '%s',
            "+a\x1B[31mb\x1B]0;title\x07c\x1BM",
            '%s',
        ];
    }
}
