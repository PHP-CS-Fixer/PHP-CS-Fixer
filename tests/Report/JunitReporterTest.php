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
use PhpCsFixer\Report\JunitReporter;
use PhpCsFixer\Report\ReportSummary;
use PHPUnit\Framework\TestCase;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\JunitReporter
 */
final class JunitReporterTest extends TestCase
{
    /**
     * @var JunitReporter
     */
    private $reporter;

    /**
     * JUnit XML schema from Jenkins.
     *
     * @var string
     *
     * @see https://github.com/jenkinsci/xunit-plugin/blob/master/src/main/resources/org/jenkinsci/plugins/xunit/types/model/xsd/junit-10.xsd
     */
    private $xsd;

    protected function setUp()
    {
        parent::setUp();

        $this->reporter = new JunitReporter();
        $this->xsd = file_get_contents(__DIR__.'/../../doc/junit-10.xsd');
    }

    /**
     * @covers \PhpCsFixer\Report\JunitReporter::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('junit', $this->reporter->getFormat());
    }

    public function testGenerateNoErrors()
    {
        $expectedReport = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite name="PHP CS Fixer" tests="1" assertions="1" failures="0" errors="0">
    <testcase name="All OK" assertions="1"/>
  </testsuite>
</testsuites>
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
<testsuites>
  <testsuite name="PHP CS Fixer" tests="1" assertions="1" failures="1" errors="0">
    <testcase name="someFile" file="someFile.php" assertions="1">
      <failure type="code_style">Wrong code style</failure>
    </testcase>
  </testsuite>
</testsuites>
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
<testsuites>
  <testsuite name="PHP CS Fixer" tests="1" assertions="1" failures="1" errors="0">
    <testcase name="someFile" file="someFile.php" assertions="1">
      <failure type="code_style"><![CDATA[Wrong code style

Diff:
---------------

this text is a diff ;)]]></failure>
    </testcase>
  </testsuite>
</testsuites>
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
<testsuites>
  <testsuite name="PHP CS Fixer" tests="1" assertions="2" failures="2" errors="0">
    <testcase name="someFile" file="someFile.php" assertions="2">
      <failure type="code_style">applied fixers:
---------------
* some_fixer_name_here_1
* some_fixer_name_here_2</failure>
    </testcase>
  </testsuite>
</testsuites>
XML;

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                array(
                    'someFile.php' => array(
                        'appliedFixers' => array('some_fixer_name_here_1', 'some_fixer_name_here_2'),
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
<testsuites>
  <testsuite name="PHP CS Fixer" tests="1" assertions="1" failures="1" errors="0" time="1.234">
    <testcase name="someFile" file="someFile.php" assertions="1">
      <failure type="code_style">Wrong code style</failure>
    </testcase>
  </testsuite>
</testsuites>
XML;

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                array(
                    'someFile.php' => array(
                        'appliedFixers' => array('some_fixer_name_here'),
                    ),
                ),
                1234,
                0,
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
<?xml version="1.0"?>
<testsuites>
  <testsuite assertions="3" errors="0" failures="3" name="PHP CS Fixer" tests="2" time="1.234">
    <testcase assertions="2" file="someFile.php" name="someFile">
      <failure type="code_style">applied fixers:
---------------
* some_fixer_name_here_1
* some_fixer_name_here_2

Diff:
---------------

this text is a diff ;)</failure>
    </testcase>
    <testcase assertions="1" file="anotherFile.php" name="anotherFile">
      <failure type="code_style">applied fixers:
---------------
* another_fixer_name_here

Diff:
---------------

another diff here ;)</failure>
    </testcase>
  </testsuite>
</testsuites>
XML;

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                array(
                    'someFile.php' => array(
                        'appliedFixers' => array('some_fixer_name_here_1', 'some_fixer_name_here_2'),
                        'diff' => 'this text is a diff ;)',
                    ),
                    'anotherFile.php' => array(
                        'appliedFixers' => array('another_fixer_name_here'),
                        'diff' => 'another diff here ;)',
                    ),
                ),
                1234,
                0,
                true,
                true,
                false
            )
        );

        $this->assertThat($actualReport, new XMLMatchesXSDConstraint($this->xsd));
        $this->assertXmlStringEqualsXmlString($expectedReport, $actualReport);
    }
}
