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
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Differ\DiffConsoleFormatter
 */
final class DiffConsoleFormatterTest extends TestCase
{
    /**
     * @dataProvider provideTestCases
     */
    public function testDiffConsoleFormatter(string $expected, bool $isDecoratedOutput, string $template, string $diff, string $lineTemplate): void
    {
        $diffFormatter = new DiffConsoleFormatter($isDecoratedOutput, $template);

        static::assertSame(
            str_replace(PHP_EOL, "\n", $expected),
            str_replace(PHP_EOL, "\n", $diffFormatter->format($diff, $lineTemplate))
        );
    }

    public function provideTestCases(): array
    {
        return [
            [
                sprintf(
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
                    OutputFormatter::escape('+A')
                ),
                true,
                sprintf(
                    '<comment>   ---------- begin diff ----------</comment>%s%%s%s<comment>   ----------- end diff -----------</comment>',
                    PHP_EOL,
                    PHP_EOL
                ),
                '
@@ -12,51 +12,151 @@
 no change
-/**\
+/*\
+A
',
                '   %s',
            ],
            [
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
                sprintf('[start]%s%%s%s[end]', PHP_EOL, PHP_EOL),
                '
@@ -12,51 +12,151 @@
 no change
 '.'
-/**\
+/*\
+A
',
                '| %s',
            ],
            [
                mb_convert_encoding("<fg=red>--- Original</fg=red>\n<fg=green>+ausgefüllt</fg=green>", 'ISO-8859-1'),
                true,
                '%s',
                mb_convert_encoding("--- Original\n+ausgefüllt", 'ISO-8859-1'),
                '%s',
            ],
            [
                mb_convert_encoding("<fg=red>--- Original</fg=red>\n<fg=green>+++ New</fg=green>\n<fg=cyan>@@ @@</fg=cyan>\n<fg=red>-ausgefüllt</fg=red>", 'ISO-8859-1'),
                true,
                '%s',
                mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
                '%s',
            ],
            [
                mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
                false,
                '%s',
                mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
                '%s',
            ],
        ];
    }
}
