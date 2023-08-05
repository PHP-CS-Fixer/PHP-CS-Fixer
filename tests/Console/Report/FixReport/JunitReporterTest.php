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

use PhpCsFixer\Console\Report\FixReport\JunitReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\PhpunitConstraintXmlMatchesXsd\Constraint\XmlMatchesXsd;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\JunitReporter
 */
final class JunitReporterTest extends AbstractReporterTestCase
{
    /**
     * JUnit XML schema from Jenkins.
     *
     * @var null|string
     *
     * @see https://github.com/jenkinsci/xunit-plugin/blob/master/src/main/resources/org/jenkinsci/plugins/xunit/types/model/xsd/junit-10.xsd
     */
    private static $xsd;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$xsd = file_get_contents(__DIR__.'/../../../../doc/schemas/fix/junit-10.xsd');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::$xsd = null;
    }

    protected function getFormat(): string
    {
        return 'junit';
    }

    protected static function createNoErrorReport(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <testsuites>
              <testsuite name="PHP CS Fixer" tests="1" assertions="1" failures="0" errors="0">
                <testcase name="All OK" assertions="1"/>
              </testsuite>
            </testsuites>
            XML;
    }

    protected static function createSimpleReport(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <testsuites>
              <testsuite name="PHP CS Fixer" tests="1" assertions="1" failures="1" errors="0">
                <testcase name="someFile" file="someFile.php" assertions="1">
                  <failure type="code_style">Wrong code style

            Diff:
            ---------------

            --- Original
            +++ New
            @@ -2,7 +2,7 @@

             class Foo
             {
            -    public function bar($foo = 1, $bar)
            +    public function bar($foo, $bar)
                 {
                 }
             }</failure>
                </testcase>
              </testsuite>
            </testsuites>
            XML;
    }

    protected static function createWithDiffReport(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <testsuites>
              <testsuite name="PHP CS Fixer" tests="1" assertions="1" failures="1" errors="0">
                <testcase name="someFile" file="someFile.php" assertions="1">
                  <failure type="code_style"><![CDATA[Wrong code style

            Diff:
            ---------------

            --- Original
            +++ New
            @@ -2,7 +2,7 @@

             class Foo
             {
            -    public function bar($foo = 1, $bar)
            +    public function bar($foo, $bar)
                 {
                 }
             }]]></failure>
                </testcase>
              </testsuite>
            </testsuites>
            XML;
    }

    protected static function createWithAppliedFixersReport(): string
    {
        return <<<'XML'
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
    }

    protected static function createWithTimeAndMemoryReport(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <testsuites>
              <testsuite name="PHP CS Fixer" tests="1" assertions="1" failures="1" errors="0" time="1.234">
                <testcase name="someFile" file="someFile.php" assertions="1">
                  <failure type="code_style">Wrong code style

            Diff:
            ---------------

            --- Original
            +++ New
            @@ -2,7 +2,7 @@

             class Foo
             {
            -    public function bar($foo = 1, $bar)
            +    public function bar($foo, $bar)
                 {
                 }
             }</failure>
                </testcase>
              </testsuite>
            </testsuites>
            XML;
    }

    protected static function createComplexReport(): string
    {
        return <<<'XML'
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
    }

    protected function assertFormat(string $expected, string $input): void
    {
        $formatter = new OutputFormatter();
        $input = $formatter->format($input);

        self::assertThat($input, new XmlMatchesXsd(self::$xsd));
        self::assertXmlStringEqualsXmlString($expected, $input);
    }

    protected function createReporter(): ReporterInterface
    {
        return new JunitReporter();
    }
}
