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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Console\Report\FixReport\ReporterFactory;
use PhpCsFixer\Documentation\DocumentationLocator;
use PhpCsFixer\Documentation\FixerDocumentGenerator;
use PhpCsFixer\Documentation\RuleSetDocumentationGenerator;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Tests\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 *
 * @coversNothing
 *
 * @group legacy
 * @group auto-review
 */
final class DocumentationTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @dataProvider provideFixerDocumentationFileIsUpToDateCases
     */
    public function testFixerDocumentationFileIsUpToDate(FixerInterface $fixer): void
    {
        // @TODO 4.0 Remove this expectations
        $this->expectDeprecation('Rule set "@PER" is deprecated. Use "@PER-CS" instead.');
        $this->expectDeprecation('Rule set "@PER:risky" is deprecated. Use "@PER-CS:risky" instead.');

        $locator = new DocumentationLocator();
        $generator = new FixerDocumentGenerator($locator);

        $path = $locator->getFixerDocumentationFilePath($fixer);

        self::assertFileExists($path);

        $expected = $generator->generateFixerDocumentation($fixer);
        $actual = file_get_contents($path);

        $expected = preg_replace_callback(
            '/
                # an example
                (?<before>
                    Example\ \#\d\n
                    ~+\n
                    \n
                    (?:\*Default\*\ configuration\.\n\n)?
                    (?:With\ configuration:.*?\n\n)?
                )
                # with a diff that could not be generated
                \.\.\ error::\n
                \ \ \ Cannot\ generate\ diff\ for\ code\ sample\ \#\d\ of\ rule\ .+?:\n
                \ \ \ the\ sample\ is\ not\ suitable\ for\ current\ version\ of\ PHP\ \(.+?\).\n
                # followed by another title or end of file
                (?<after>
                    \n
                    [^ \n].*?
                    \n
                    |$
                )
            /x',
            static function (array $matches) use ($actual): string {
                $before = preg_quote($matches['before'], '/');
                $after = preg_quote($matches['after'], '/');

                $replacement = '[UNAVAILABLE EXAMPLE DIFF]';

                if (1 === preg_match("/{$before}(\\.\\. code-block:: diff.*?){$after}/s", $actual, $actualMatches)) {
                    $replacement = $actualMatches[1];
                }

                return $matches[1].$replacement.$matches[2];
            },
            $expected
        );

        self::assertSame($expected, $actual);
    }

    public static function provideFixerDocumentationFileIsUpToDateCases(): iterable
    {
        foreach (self::getFixers() as $fixer) {
            yield $fixer->getName() => [$fixer];
        }
    }

    public function testFixersDocumentationIndexFileIsUpToDate(): void
    {
        $locator = new DocumentationLocator();
        $generator = new FixerDocumentGenerator($locator);

        self::assertFileEqualsString(
            $generator->generateFixersDocumentationIndex(self::getFixers()),
            $locator->getFixersDocumentationIndexFilePath()
        );
    }

    public function testFixersDocumentationDirectoryHasNoExtraFiles(): void
    {
        $generator = new DocumentationLocator();

        self::assertCount(
            \count(self::getFixers()) + 1,
            (new Finder())->files()->in($generator->getFixersDocumentationDirectoryPath())
        );
    }

    public function testRuleSetsDocumentationIsUpToDate(): void
    {
        $locator = new DocumentationLocator();
        $generator = new RuleSetDocumentationGenerator($locator);

        $fixers = self::getFixers();
        $paths = [];

        foreach (RuleSets::getSetDefinitions() as $name => $definition) {
            $path = $locator->getRuleSetsDocumentationFilePath($name);
            $paths[$path] = $definition;

            self::assertFileEqualsString(
                $generator->generateRuleSetsDocumentation($definition, $fixers),
                $path,
                sprintf('RuleSet documentation is generated (please see CONTRIBUTING.md), file "%s".', $path)
            );
        }

        $indexFilePath = $locator->getRuleSetsDocumentationIndexFilePath();

        self::assertFileEqualsString(
            $generator->generateRuleSetsDocumentationIndex($paths),
            $indexFilePath,
            sprintf('RuleSet documentation is generated (please CONTRIBUTING.md), file "%s".', $indexFilePath)
        );
    }

    public function testRuleSetsDocumentationDirectoryHasNoExtraFiles(): void
    {
        $generator = new DocumentationLocator();

        self::assertCount(
            \count(RuleSets::getSetDefinitions()) + 1,
            (new Finder())->files()->in($generator->getRuleSetsDocumentationDirectoryPath())
        );
    }

    public function testInstallationDocHasCorrectMinimumVersion(): void
    {
        $composerJsonContent = file_get_contents(__DIR__.'/../../composer.json');
        $composerJson = json_decode($composerJsonContent, true, 512, JSON_THROW_ON_ERROR);
        $phpVersion = $composerJson['require']['php'];
        $minimumVersion = ltrim(substr($phpVersion, 0, strpos($phpVersion, ' ')), '^');

        $minimumVersionInformation = sprintf('PHP needs to be a minimum version of PHP %s.', $minimumVersion);
        $installationDocPath = realpath(__DIR__.'/../../doc/installation.rst');

        self::assertStringContainsString(
            $minimumVersionInformation,
            file_get_contents($installationDocPath),
            sprintf('Files %s needs to contain information "%s"', $installationDocPath, $minimumVersionInformation)
        );
    }

    public function testAllReportFormatsAreInUsageDoc(): void
    {
        $locator = new DocumentationLocator();
        $usage = $locator->getUsageFilePath();
        self::assertFileExists($usage);

        $usage = file_get_contents($usage);
        self::assertIsString($usage);

        $reporterFactory = new ReporterFactory();
        $reporterFactory->registerBuiltInReporters();

        $formats = array_filter(
            $reporterFactory->getFormats(),
            static fn (string $format): bool => 'txt' !== $format,
        );

        foreach ($formats as $format) {
            self::assertStringContainsString(sprintf('* ``%s``', $format), $usage);
        }

        $lastFormat = array_pop($formats);
        $expectedContent = 'Supported formats are ``txt`` (default one), ';

        foreach ($formats as $format) {
            $expectedContent .= '``'.$format.'``, ';
        }

        $expectedContent = substr($expectedContent, 0, -2);
        $expectedContent .= ' and ``'.$lastFormat.'``.';

        self::assertStringContainsString($expectedContent, $usage);
    }

    private static function assertFileEqualsString(string $expectedString, string $actualFilePath, string $message = ''): void
    {
        self::assertFileExists($actualFilePath, $message);
        self::assertSame($expectedString, file_get_contents($actualFilePath), $message);
    }

    /**
     * @return list<FixerInterface>
     */
    private static function getFixers(): array
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        return $factory->getFixers();
    }
}
