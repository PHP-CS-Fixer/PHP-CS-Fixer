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
use PhpCsFixer\Report\XmlReport;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class XmlReportTest extends \PHPUnit_Framework_TestCase
{
    /** @var XmlReport */
    private $report;

    protected function setUp()
    {
        $this->report = new XmlReport();
    }

    /**
     * @covers PhpCsFixer\Report\XmlReport::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('xml', $this->report->getFormat());
    }

    public function testProcessSimple()
    {
        $expectedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php"/>
  </files>
</report>
XML;

        $this->assertXmlStringEqualsXmlString(
            $expectedXml,
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
        $expectedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php">
      <diff><![CDATA[this text is a diff ;)]]></diff>
    </file>
  </files>
</report>
XML;

        $this->assertXmlStringEqualsXmlString(
            $expectedXml,
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
        $expectedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php">
      <applied_fixers>
        <applied_fixer name="some_fixer_name_here"/>
      </applied_fixers>
    </file>
  </files>
</report>
XML;

        $this->assertXmlStringEqualsXmlString(
            $expectedXml,
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
        $expectedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php"/>
  </files>
  <time unit="s">
    <total value="1.234"/>
  </time>
  <memory value="2.5" unit="MB"/>
</report>
XML;

        $this->assertXmlStringEqualsXmlString(
            $expectedXml,
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

    public function testProcessComplex()
    {
        $expectedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php">
      <applied_fixers>
        <applied_fixer name="some_fixer_name_here"/>
      </applied_fixers>
      <diff>this text is a diff ;)</diff>
    </file>
    <file id="2" name="anotherFile.php">
      <applied_fixers>
        <applied_fixer name="another_fixer_name_here"/>
      </applied_fixers>
      <diff>another diff here ;)</diff>
    </file>
  </files>
  <time unit="s">
    <total value="1.234"/>
  </time>
  <memory value="2.5" unit="MB"/>
</report>
XML;

        $this->assertXmlStringEqualsXmlString(
            $expectedXml,
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
                    ->setMemory(2.5 * 1024 * 1024)
                    ->setTime(1234)
            )
        );
    }
}
