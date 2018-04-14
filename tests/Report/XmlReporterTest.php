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

use PhpCsFixer\PhpunitConstraintXmlMatchesXsd\Constraint\XmlMatchesXsd;
use PhpCsFixer\Report\XmlReporter;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\XmlReporter
 */
final class XmlReporterTest extends AbstractReporterTestCase
{
    /**
     * @var string
     */
    private static $xsd;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // @TODO 2.11 remove me
        if (!class_exists('PhpCsFixer\PhpunitConstraintXmlMatchesXsd\Constraint\XmlMatchesXsd')) {
            self::markTestSkipped('Cannot execute test, install `php-cs-fixer/phpunit-constraint-xmlmatchesxsd` first.');
        }

        self::$xsd = file_get_contents(__DIR__.'/../../doc/xml.xsd');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::$xsd = null;
    }

    public function createNoErrorReport()
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files />
</report>
XML;
    }

    public function createSimpleReport()
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php"/>
  </files>
</report>
XML;
    }

    public function createWithDiffReport()
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php">
      <diff><![CDATA[this text is a diff ;)]]></diff>
    </file>
  </files>
</report>
XML;
    }

    public function createWithAppliedFixersReport()
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php">
      <applied_fixers>
        <applied_fixer name="some_fixer_name_here_1"/>
        <applied_fixer name="some_fixer_name_here_2"/>
      </applied_fixers>
    </file>
  </files>
</report>
XML;
    }

    public function createWithTimeAndMemoryReport()
    {
        return <<<'XML'
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
    }

    public function createComplexReport()
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<report>
  <files>
    <file id="1" name="someFile.php">
      <applied_fixers>
        <applied_fixer name="some_fixer_name_here_1"/>
        <applied_fixer name="some_fixer_name_here_2"/>
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
    }

    protected function createReporter()
    {
        return new XmlReporter();
    }

    protected function getFormat()
    {
        return 'xml';
    }

    protected function assertFormat($expected, $input)
    {
        $formatter = new OutputFormatter();
        $input = $formatter->format($input);

        $this->assertThat($input, new XmlMatchesXsd(self::$xsd));
        $this->assertXmlStringEqualsXmlString($expected, $input);
    }
}
