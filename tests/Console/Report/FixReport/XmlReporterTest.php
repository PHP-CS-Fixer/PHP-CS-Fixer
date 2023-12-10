<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Console\Report\FixReport;

use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\Console\Report\FixReport\XmlReporter;
use PhpCsFixer\PhpunitConstraintXmlMatchesXsd\Constraint\XmlMatchesXsd;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\XmlReporter
 */
final class XmlReporterTest extends AbstractReporterTestCase
{
    /**
     * @var null|string
     */
    private static $xsd;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$xsd = file_get_contents(__DIR__.'/../../../../doc/schemas/fix/xml.xsd');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::$xsd = null;
    }

    protected static function createNoErrorReport(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <report>
              <files />
            </report>
            XML;
    }

    protected static function createSimpleReport(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <report>
              <files>
                <file id="1" name="someFile.php">
                  <diff>--- Original
            +++ New
            @@ -2,7 +2,7 @@

             class Foo
             {
            -    public function bar($foo = 1, $bar)
            +    public function bar($foo, $bar)
                 {
                 }
             }</diff>
                </file>
              </files>
            </report>
            XML;
    }

    protected static function createWithDiffReport(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <report>
              <files>
                <file id="1" name="someFile.php">
                  <diff>--- Original
            +++ New
            @@ -2,7 +2,7 @@

             class Foo
             {
            -    public function bar($foo = 1, $bar)
            +    public function bar($foo, $bar)
                 {
                 }
             }</diff>
                </file>
              </files>
            </report>
            XML;
    }

    protected static function createWithAppliedFixersReport(): string
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

    protected static function createWithTimeAndMemoryReport(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <report>
              <files>
                <file id="1" name="someFile.php">
                  <diff>--- Original
            +++ New
            @@ -2,7 +2,7 @@

             class Foo
             {
            -    public function bar($foo = 1, $bar)
            +    public function bar($foo, $bar)
                 {
                 }
             }</diff>
                </file>
              </files>
              <time unit="s">
                <total value="1.234"/>
              </time>
              <memory value="2.5" unit="MB"/>
            </report>
            XML;
    }

    protected static function createComplexReport(): string
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

    protected function createReporter(): ReporterInterface
    {
        return new XmlReporter();
    }

    protected function getFormat(): string
    {
        return 'xml';
    }

    protected function assertFormat(string $expected, string $input): void
    {
        $formatter = new OutputFormatter();
        $input = $formatter->format($input);

        self::assertThat($input, new XmlMatchesXsd(self::$xsd));
        self::assertXmlStringEqualsXmlString($expected, $input);
    }
}
