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

use PhpCsFixer\Report\JunitReporter;
use PhpCsFixer\Report\ReportSummary;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class JunitReporterTest extends \PHPUnit_Framework_TestCase
{
    /** @var JunitReporter */
    private $reporter;

    protected function setUp()
    {
        $this->reporter = new JunitReporter();
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

        $this->assertJunitXmlSchema($actualReport);
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

        $this->assertJunitXmlSchema($actualReport);
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

        $this->assertJunitXmlSchema($actualReport);
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

        $this->assertJunitXmlSchema($actualReport);
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

        $this->assertJunitXmlSchema($actualReport);
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

        $this->assertJunitXmlSchema($actualReport);
        $this->assertXmlStringEqualsXmlString($expectedReport, $actualReport);
    }

    /**
     * Validates generated xml report with schema.
     * Uses JUnit XML schema from Jenkins.
     *
     * @see https://github.com/jenkinsci/xunit-plugin/blob/master/src/main/resources/org/jenkinsci/plugins/xunit/types/model/xsd/junit-10.xsd
     *
     * @param string $xml
     */
    private function assertJunitXmlSchema($xml)
    {
        $xsdPath = __DIR__.'/../../doc/junit-10.xsd';

        static $errorLevels = array(
            LIBXML_ERR_WARNING => 'Warning',
            LIBXML_ERR_ERROR => 'Error',
            LIBXML_ERR_FATAL => 'Fatal Error',
        );

        $internal = libxml_use_internal_errors(true);

        $dom = new \DOMDocument();

        $loaded = $dom->loadXML($xml);
        if (true !== $loaded) {
            libxml_use_internal_errors($internal);
            $this->fail(sprintf('XML loading failed, expected "true", got "%s".', var_export($loaded, true)));
        }

        $dom->schemaValidate($xsdPath);

        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf(
                '%s #%s: %s (%s:%s)',
                isset($errorLevels[$error->level]) ? $errorLevels[$error->level] : null,
                $error->code,
                trim($error->message),
                $error->file,
                $error->line
            );
        }

        $errors = implode(PHP_EOL, $errors);

        libxml_clear_errors();
        libxml_use_internal_errors($internal);

        if (strlen($errors) > 0) {
            $this->fail('Actual xml does not match schema: '.PHP_EOL.$errors);
        }
    }
}
