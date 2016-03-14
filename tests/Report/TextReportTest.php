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
        $expectedtext = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
           
   1) someFile.php

TEXT
        );

        $this->report->setChanged(
            array(
                'someFile.php' => array(
                    'appliedFixers' => array('some_fixer_name_here'),
                ),
            )
        );

        $this->assertSame($expectedtext, $this->report->generate());
    }

    public function testProcessWithDiff()
    {
        $expectedtext = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
           
   1) someFile.php
      ---------- begin diff ----------
this text is a diff ;)
      ----------- end diff -----------

TEXT
        );

        $this->report->setChanged(
            array(
                'someFile.php' => array(
                    'appliedFixers' => array('some_fixer_name_here'),
                    'diff' => 'this text is a diff ;)',
                ),
            )
        );

        $this->assertSame($expectedtext, $this->report->generate());
    }

    public function testProcessWithAppliedFixers()
    {
        $this->report->setAddAppliedFixers(true);

        $expectedtext = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
           
   1) someFile.php (some_fixer_name_here)

TEXT
        );

        $this->report->setChanged(
            array(
                'someFile.php' => array(
                    'appliedFixers' => array('some_fixer_name_here'),
                ),
            )
        );

        $this->assertSame($expectedtext, $this->report->generate());
    }

    public function testProcessWithTimeAndMemory()
    {
        $this->report
            ->setTime(1234)
            ->setMemory(2.5 * 1024 * 1024);

        $expectedtext = str_replace(
            "\n",
            PHP_EOL,
            <<<'TEXT'
           
   1) someFile.php

Fixed all files in 1.234 seconds, 2.500 MB memory used

TEXT
        );

        $this->report->setChanged(
            array(
                'someFile.php' => array(
                    'appliedFixers' => array('some_fixer_name_here'),
                ),
            )
        );

        $this->assertSame($expectedtext, $this->report->generate());
    }

    public function testProcessComplexWithDecoratedOutput()
    {
        $this->report
            ->setAddAppliedFixers(true)
            ->setIsDryRun(true)
            ->setIsDecoratedOutput(true)
            ->setTime(1234)
            ->setMemory(2.5 * 1024 * 1024);

        $expectedtext = str_replace(
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

        $this->report->setChanged(
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
        );

        $this->assertSame($expectedtext, $this->report->generate());
    }
}
