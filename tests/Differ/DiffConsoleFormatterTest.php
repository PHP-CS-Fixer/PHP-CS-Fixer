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

namespace PhpCsFixer\Tests\Differ;

use PhpCsFixer\Differ\DiffConsoleFormatter;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Differ\DiffConsoleFormatter
 */
final class DiffConsoleFormatterTest extends TestCase
{
    /**
     * @dataProvider provideTestCases
     *
     * @param string $expected
     * @param bool   $isDecoratedOutput
     * @param string $template
     * @param string $diff
     * @param string $lineTemplate
     */
    public function testDiffConsoleFormatter($expected, $isDecoratedOutput, $template, $diff, $lineTemplate)
    {
        $diffFormatter = new DiffConsoleFormatter($isDecoratedOutput, $template);

        $this->assertSame(
            str_replace(PHP_EOL, "\n", $expected),
            str_replace(PHP_EOL, "\n", $diffFormatter->format($diff, $lineTemplate))
        );
    }

    public function provideTestCases()
    {
        return array(
            array(
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
            ),
            array(
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
            ),
        );
    }
}
