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

use PhpCsFixer\Documentation\DocumentationGenerator;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSetsFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 * @requires PHP 7.3
 */
final class DocumentationTest extends TestCase
{
    /**
     * @var RuleSetsFactory
     */
    private static $ruleSetsFactory;

    public static function setUpBeforeClass(): void
    {
        self::$ruleSetsFactory = new RuleSetsFactory();
        self::$ruleSetsFactory->registerBuiltInRuleSets();
    }

    /**
     * @dataProvider provideFixerCases
     */
    public function testFixerDocumentationFileIsUpToDate(FixerInterface $fixer): void
    {
        $generator = new DocumentationGenerator();

        $path = $generator->getFixerDocumentationFilePath($fixer);

        static::assertFileExists($path);

        $expected = $generator->generateFixerDocumentation($fixer, self::$ruleSetsFactory);
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
            function (array $matches) use ($actual) {
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

        static::assertSame($expected, $actual);
    }

    public function provideFixerCases()
    {
        $cases = [];

        foreach ($this->getFixers() as $fixer) {
            $cases[$fixer->getName()] = [$fixer];
        }

        return $cases;
    }

    public function testFixersDocumentationIndexFileIsUpToDate(): void
    {
        $generator = new DocumentationGenerator();

        self::assertFileEqualsString(
            $generator->generateFixersDocumentationIndex($this->getFixers()),
            $generator->getFixersDocumentationIndexFilePath()
        );
    }

    /**
     * @requires PHP 7.4
     */
    public function testFixersDocumentationDirectoryHasNoExtraFiles(): void
    {
        $generator = new DocumentationGenerator();

        static::assertCount(
            \count($this->getFixers()) + 1,
            (new Finder())->files()->in($generator->getFixersDocumentationDirectoryPath())
        );
    }

    /**
     * @requires PHP 7.4
     */
    public function testRuleSetsDocumentationIsUpToDate(): void
    {
        $fixers = $this->getFixers();
        $generator = new DocumentationGenerator();
        $paths = [];

        foreach (self::$ruleSetsFactory->getRuleSets() as $name => $definition) {
            $paths[$name] = $path = $generator->getRuleSetsDocumentationFilePath($name);

            static::assertFileEqualsString(
                $generator->generateRuleSetsDocumentation($definition, $fixers),
                $path,
                sprintf('RuleSet documentation is generated (please see CONTRIBUTING.md), file "%s".', $path)
            );
        }

        $indexFilePath = $generator->getRuleSetsDocumentationIndexFilePath();

        static::assertFileEqualsString(
            $generator->generateRuleSetsDocumentationIndex($paths),
            $indexFilePath,
            sprintf('RuleSet documentation is generated (please CONTRIBUTING.md), file "%s".', $indexFilePath)
        );
    }

    /**
     * @requires PHP 7.4
     */
    public function testRuleSetsDocumentationDirectoryHasNoExtraFiles(): void
    {
        $generator = new DocumentationGenerator();

        static::assertCount(
            \count(self::$ruleSetsFactory->getRuleSets()) + 1,
            (new Finder())->files()->in($generator->getRuleSetsDocumentationDirectoryPath())
        );
    }

    private static function assertFileEqualsString(string $expectedString, string $actualFilePath, string $message = ''): void
    {
        static::assertFileExists($actualFilePath, $message);
        static::assertSame($expectedString, file_get_contents($actualFilePath), $message);
    }

    private function getFixers()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        return $factory->getFixers();
    }
}
