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
use PhpCsFixer\Report\CheckstyleReporter;
use PhpCsFixer\Report\ReportSummary;
use PHPUnit\Framework\TestCase;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\CheckstyleReporter
 */
final class CheckstyleReporterTest extends TestCase
{
    /**
     * @var CheckstyleReporter
     */
    private $reporter;

    /**
     * "checkstyle" XML schema.
     *
     * @var string
     */
    private $xsd;

    protected function setUp()
    {
        $this->reporter = new CheckstyleReporter();
        $this->xsd = file_get_contents(__DIR__.'/../../doc/checkstyle.xsd');
    }

    /**
     * @covers \PhpCsFixer\Report\CheckstyleReporter::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('checkstyle', $this->reporter->getFormat());
    }

    public function testGenerateNoErrors()
    {
        $expectedReport = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<checkstyle />
XML;

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [],
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
<checkstyle>
  <file name="someFile.php">
    <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here_1" message="Found violation(s) of type: some_fixer_name_here_1" />
    <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here_2" message="Found violation(s) of type: some_fixer_name_here_2" />
  </file>
</checkstyle>
XML;

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here_1', 'some_fixer_name_here_2'],
                    ],
                ],
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
<checkstyle>
  <file name="someFile.php">
    <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here_1" message="Found violation(s) of type: some_fixer_name_here_1" />
    <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here_2" message="Found violation(s) of type: some_fixer_name_here_2" />
  </file>
</checkstyle>
XML;

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here_1', 'some_fixer_name_here_2'],
                    ],
                ],
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
}
