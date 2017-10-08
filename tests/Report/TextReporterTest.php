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

namespace PhpCsFixer\Tests\Report;

use PhpCsFixer\Report\TextReporter;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\TextReporter
 */
final class TextReporterTest extends AbstractReporterTestCase
{
    public function createNoErrorReport()
    {
        return <<<'TEXT'
TEXT;
    }

    public function createSimpleReport()
    {
        return str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php

TEXT
        );
    }

    public function createWithDiffReport()
    {
        return str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php
      ---------- begin diff ----------
this text is a diff ;)
      ----------- end diff -----------


TEXT
        );
    }

    public function createWithAppliedFixersReport()
    {
        return str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php (some_fixer_name_here_1, some_fixer_name_here_2)

TEXT
        );
    }

    public function createWithTimeAndMemoryReport()
    {
        return str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php

Fixed all files in 1.234 seconds, 2.500 MB memory used

TEXT
        );
    }

    public function createComplexReport()
    {
        return str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php (<comment>some_fixer_name_here_1, some_fixer_name_here_2</comment>)
<comment>      ---------- begin diff ----------</comment>
this text is a diff ;)
<comment>      ----------- end diff -----------</comment>

   2) anotherFile.php (<comment>another_fixer_name_here</comment>)
<comment>      ---------- begin diff ----------</comment>
another diff here ;)
<comment>      ----------- end diff -----------</comment>


Checked all files in 1.234 seconds, 2.500 MB memory used

TEXT
        );
    }

    protected function createReporter()
    {
        return new TextReporter();
    }

    protected function getFormat()
    {
        return 'txt';
    }

    protected function assertFormat($expected, $input)
    {
        $this->assertSame($expected, $input);
    }
}
