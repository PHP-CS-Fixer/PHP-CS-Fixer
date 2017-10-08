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

use GeckoPackages\PHPUnit\Constraints\XML\XMLMatchesXSDConstraint;
use PhpCsFixer\Report\ReportSummary;
use PhpCsFixer\Report\XmlReporter;
use PHPUnit\Framework\TestCase;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\XmlReporter
 */
final class XmlReporterTest extends TestCase
{
    /** @var XmlReporter */
    private $reporter;

    /**
     * @var string
     */
    private $xsd;

    protected function setUp()
    {
        parent::setUp();

        $this->reporter = new XmlReporter();
        $this->xsd = file_get_contents(__DIR__.'/../../doc/xml.xsd');
    }

    /**
     * @covers \PhpCsFixer\Report\XmlReporter::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('xml', $this->reporter->getFormat());
    }

    public function testGenerateNoErrors()
    {
        $expectedReport = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files />
</report>
XML;

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                array(),
                0,
                0,
                false,
                false,
                false
            )
        );

        $this->assertThat($actualReport, new XMLMatchesXSDConstraint($this->xsd));
        $this->assertXmlStringEqualsXmlString($expectedReport, $actualReport);
    }

    public function testGenerateSimple()
    {
        $expectedReport = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php"/>
  </files>
</report>
XML;

        $actualReport = $this->reporter->generate(
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
        );

        $this->assertThat($actualReport, new XMLMatchesXSDConstraint($this->xsd));
        $this->assertXmlStringEqualsXmlString($expectedReport, $actualReport);
    }

    public function testGenerateWithDiff()
    {
        $expectedReport = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php">
      <diff><![CDATA[this text is a diff ;)]]></diff>
    </file>
  </files>
</report>
XML;

        $actualReport = $this->reporter->generate(
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
        );

        $this->assertThat($actualReport, new XMLMatchesXSDConstraint($this->xsd));
        $this->assertXmlStringEqualsXmlString($expectedReport, $actualReport);
    }

    public function testGenerateWithAppliedFixers()
    {
        $expectedReport = <<<'XML'
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

        $actualReport = $this->reporter->generate(
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
        );

        $this->assertThat($actualReport, new XMLMatchesXSDConstraint($this->xsd));
        $this->assertXmlStringEqualsXmlString($expectedReport, $actualReport);
    }

    public function testGenerateWithTimeAndMemory()
    {
        $expectedReport = <<<'XML'
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

        $actualReport = $this->reporter->generate(
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
        );

        $this->assertThat($actualReport, new XMLMatchesXSDConstraint($this->xsd));
        $this->assertXmlStringEqualsXmlString($expectedReport, $actualReport);
    }

    public function testGenerateComplex()
    {
        $expectedReport = <<<'XML'
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
        <applied_fixer name="another_fixer_name_here_1"/>
        <applied_fixer name="another_fixer_name_here_2"/>
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

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                array(
                    'someFile.php' => array(
                        'appliedFixers' => array('some_fixer_name_here'),
                        'diff' => 'this text is a diff ;)',
                    ),
                    'anotherFile.php' => array(
                        'appliedFixers' => array('another_fixer_name_here_1', 'another_fixer_name_here_2'),
                        'diff' => 'another diff here ;)',
                    ),
                ),
                1234,
                2.5 * 1024 * 1024,
                true,
                false,
                false
            )
        );

        $this->assertThat($actualReport, new XMLMatchesXSDConstraint($this->xsd));
        $this->assertXmlStringEqualsXmlString($expectedReport, $actualReport);
    }
}
