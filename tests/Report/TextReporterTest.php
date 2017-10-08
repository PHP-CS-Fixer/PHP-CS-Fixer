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

use PhpCsFixer\Report\ReportSummary;
use PhpCsFixer\Report\TextReporter;
use PHPUnit\Framework\TestCase;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\TextReporter
 */
final class TextReporterTest extends TestCase
{
    /** @var TextReporter */
    private $reporter;

    protected function setUp()
    {
        parent::setUp();

        $this->reporter = new TextReporter();
    }

    /**
     * @covers \PhpCsFixer\Report\TextReporter::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('txt', $this->reporter->getFormat());
    }

    public function testGenerateNoErrors()
    {
        $expectedReport = <<<'TEXT'
TEXT;

        $this->assertSame(
            $expectedReport,
            $this->reporter->generate(
                new ReportSummary(
                    array(),
                    0,
                    0,
                    false,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateSimple()
    {
        $expectedReport = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php

TEXT
        );

        $this->assertSame(
            $expectedReport,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                        ),
                    ),
                    0,
                    0,
                    false,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateWithDiff()
    {
        $expectedReport = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php
      ---------- begin diff ----------
this text is a diff ;)
      ----------- end diff -----------


TEXT
        );

        $this->assertSame(
            $expectedReport,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                            'diff' => 'this text is a diff ;)',
                        ),
                    ),
                    0,
                    0,
                    false,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateWithAppliedFixers()
    {
        $expectedReport = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php (some_fixer_name_here)

TEXT
        );

        $this->assertSame(
            $expectedReport,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                        ),
                    ),
                    0,
                    0,
                    true,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateWithTimeAndMemory()
    {
        $expectedReport = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php

Fixed all files in 1.234 seconds, 2.500 MB memory used

TEXT
        );

        $this->assertSame(
            $expectedReport,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                        ),
                    ),
                    1234,
                    2.5 * 1024 * 1024,
                    false,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateComplexWithDecoratedOutput()
    {
        $expectedReport = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
   1) someFile.php (<comment>some_fixer_name_here</comment>)
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

        $this->assertSame(
            $expectedReport,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                            'diff' => 'this text is a diff ;)',
                        ),
                        'anotherFile.php' => array(
                            'appliedFixers' => array('another_fixer_name_here'),
                            'diff' => 'another diff here ;)',
                        ),
                    ),
                    1234,
                    2.5 * 1024 * 1024,
                    true,
                    true,
                    true
                )
            )
        );
    }
}
