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

use PhpCsFixer\Report\ReportConfig;
use PhpCsFixer\Report\TextReport;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class TextReportTest extends \PHPUnit_Framework_TestCase
{
    /** @var TextReport */
    private $report;

    protected function setUp()
    {
        $this->report = new TextReport();
    }

    /**
     * @covers PhpCsFixer\Report\TextReport::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('txt', $this->report->getFormat());
    }

    public function testProcessSimple()
    {
        $expectedText = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
              1) someFile.php

TEXT
        );

        $this->assertSame(
            $expectedText,
            $this->report->generate(
                ReportConfig::create()
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                            ),
                        )
                    )
            )
        );
    }

    public function testProcessWithDiff()
    {
        $expectedText = str_replace(
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
            $expectedText,
            $this->report->generate(
                ReportConfig::create()
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                                'diff' => 'this text is a diff ;)',
                            ),
                        )
                    )
            )
        );
    }

    public function testProcessWithAppliedFixers()
    {
        $expectedText = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
              1) someFile.php (some_fixer_name_here)

TEXT
        );

        $this->assertSame(
            $expectedText,
            $this->report->generate(
                ReportConfig::create()
                    ->setAddAppliedFixers(true)
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                            ),
                        )
                    )
            )
        );
    }

    public function testProcessWithTimeAndMemory()
    {
        $expectedText = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
              1) someFile.php

Fixed all files in 1.234 seconds, 2.500 MB memory used

TEXT
        );

        $this->assertSame(
            $expectedText,
            $this->report->generate(
                ReportConfig::create()
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                            ),
                        )
                    )
                    ->setMemory(2.5 * 1024 * 1024)
                    ->setTime(1234)
            )
        );
    }

    public function testProcessComplexWithDecoratedOutput()
    {
        $expectedText = str_replace(
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
            $expectedText,
            $this->report->generate(
                ReportConfig::create()
                    ->setAddAppliedFixers(true)
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                                'diff' => 'this text is a diff ;)',
                            ),
                            'anotherFile.php' => array(
                                'appliedFixers' => array('another_fixer_name_here'),
                                'diff' => 'another diff here ;)',
                            ),
                        )
                    )
                    ->setIsDecoratedOutput(true)
                    ->setIsDryRun(true)
                    ->setMemory(2.5 * 1024 * 1024)
                    ->setTime(1234)
            )
        );
    }
}
