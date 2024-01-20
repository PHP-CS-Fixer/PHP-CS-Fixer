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

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Report\FixReport\CheckstyleReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\PhpunitConstraintXmlMatchesXsd\Constraint\XmlMatchesXsd;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\CheckstyleReporter
 */
final class CheckstyleReporterTest extends AbstractReporterTestCase
{
    /**
     * "checkstyle" XML schema.
     *
     * @var null|string
     */
    private static $xsd;

    public static function setUpBeforeClass(): void
    {
        self::$xsd = file_get_contents(__DIR__.'/../../../../doc/schemas/fix/checkstyle.xsd');
    }

    public static function tearDownAfterClass(): void
    {
        self::$xsd = null;
    }

    protected static function createNoErrorReport(): string
    {
        $about = Application::getAbout();

        return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <checkstyle version="{$about}" />
            XML;
    }

    protected static function createSimpleReport(): string
    {
        $about = Application::getAbout();

        return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <checkstyle version="{$about}">
              <file name="someFile.php">
                <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here" message="Found violation(s) of type: some_fixer_name_here" />
              </file>
            </checkstyle>
            XML;
    }

    protected static function createWithDiffReport(): string
    {
        $about = Application::getAbout();

        // NOTE: checkstyle format does NOT include diffs
        return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <checkstyle version="{$about}">
              <file name="someFile.php">
                <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here" message="Found violation(s) of type: some_fixer_name_here" />
              </file>
            </checkstyle>
            XML;
    }

    protected static function createWithAppliedFixersReport(): string
    {
        $about = Application::getAbout();

        return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <checkstyle version="{$about}">
              <file name="someFile.php">
                <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here_1" message="Found violation(s) of type: some_fixer_name_here_1" />
                <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here_2" message="Found violation(s) of type: some_fixer_name_here_2" />
              </file>
            </checkstyle>
            XML;
    }

    protected static function createWithTimeAndMemoryReport(): string
    {
        $about = Application::getAbout();

        // NOTE: checkstyle format does NOT include time or memory
        return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <checkstyle version="{$about}">
              <file name="someFile.php">
                <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here" message="Found violation(s) of type: some_fixer_name_here" />
              </file>
            </checkstyle>
            XML;
    }

    protected static function createComplexReport(): string
    {
        $about = Application::getAbout();

        return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <checkstyle version="{$about}">
              <file name="someFile.php">
                <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here_1" message="Found violation(s) of type: some_fixer_name_here_1" />
                <error severity="warning" source="PHP-CS-Fixer.some_fixer_name_here_2" message="Found violation(s) of type: some_fixer_name_here_2" />
              </file>
              <file name="anotherFile.php">
                <error severity="warning" source="PHP-CS-Fixer.another_fixer_name_here" message="Found violation(s) of type: another_fixer_name_here" />
              </file>
            </checkstyle>
            XML;
    }

    protected function createReporter(): ReporterInterface
    {
        return new CheckstyleReporter();
    }

    protected function getFormat(): string
    {
        return 'checkstyle';
    }

    protected function assertFormat(string $expected, string $input): void
    {
        $formatter = new OutputFormatter();
        $input = $formatter->format($input);

        self::assertThat($input, new XmlMatchesXsd(self::$xsd));
        self::assertXmlStringEqualsXmlString($expected, $input);
    }
}
