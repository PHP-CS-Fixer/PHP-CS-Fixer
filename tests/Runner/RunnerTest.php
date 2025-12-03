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

namespace PhpCsFixer\Tests\Runner;

use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Config\RuleCustomisationPolicyInterface;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\NullDiffer;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Fixer;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Runner\Event\AnalysisStarted;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Runner
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RunnerTest extends TestCase
{
    /**
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatFixSuccessfully(): void
    {
        $linter = $this->createLinterDouble();

        $fixers = [
            new Fixer\ClassNotation\ModifierKeywordsFixer(),
            new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
        ];

        $expectedChangedInfo = [
            'appliedFixers' => ['modifier_keywords'],
            'diff' => '',
        ];

        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'fix';
        $runner = new Runner(
            Finder::create()->in($path),
            $fixers,
            new NullDiffer(),
            null,
            new ErrorsManager(),
            $linter,
            true,
            new NullCacheManager(),
            new Directory($path),
            false
        );

        $changed = $runner->fix();

        self::assertCount(2, $changed);
        self::assertSame($expectedChangedInfo, array_pop($changed));
        self::assertSame($expectedChangedInfo, array_pop($changed));

        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'fix';
        $runner = new Runner(
            Finder::create()->in($path),
            $fixers,
            new NullDiffer(),
            null,
            new ErrorsManager(),
            $linter,
            true,
            new NullCacheManager(),
            new Directory($path),
            true
        );

        $changed = $runner->fix();

        self::assertCount(1, $changed);
        self::assertSame($expectedChangedInfo, array_pop($changed));
    }

    /**
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     * @covers \PhpCsFixer\Runner\Runner::fixSequential
     */
    public function testThatSequentialFixOfInvalidFileReportsToErrorManager(): void
    {
        $errorsManager = new ErrorsManager();

        $path = realpath(__DIR__.\DIRECTORY_SEPARATOR.'..').\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'invalid';
        $runner = new Runner(
            Finder::create()->in($path),
            [
                new Fixer\ClassNotation\ModifierKeywordsFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ],
            new NullDiffer(),
            null,
            $errorsManager,
            new Linter(),
            true,
            new NullCacheManager()
        );
        $changed = $runner->fix();
        $pathToInvalidFile = $path.\DIRECTORY_SEPARATOR.'somefile.php';

        self::assertCount(0, $changed);

        $errors = $errorsManager->getInvalidErrors();

        self::assertCount(1, $errors);

        $error = $errors[0];

        self::assertSame(Error::TYPE_INVALID, $error->getType());
        self::assertSame($pathToInvalidFile, $error->getFilePath());
    }

    /**
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     * @covers \PhpCsFixer\Runner\Runner::fixParallel
     */
    public function testThatParallelFixOfInvalidFileReportsToErrorManager(): void
    {
        $errorsManager = new ErrorsManager();

        $path = realpath(__DIR__.\DIRECTORY_SEPARATOR.'..').\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'invalid';
        $runner = new Runner(
            Finder::create()->in($path),
            [
                new Fixer\ClassNotation\ModifierKeywordsFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ],
            new NullDiffer(),
            null,
            $errorsManager,
            new Linter(),
            true,
            new NullCacheManager(),
            null,
            false,
            new ParallelConfig(2, 1, 50),
            new ArrayInput(['--config' => ConfigurationResolver::IGNORE_CONFIG_FILE], (new FixCommand(new ToolInfo()))->getDefinition())
        );
        $changed = $runner->fix();
        $pathToInvalidFile = $path.\DIRECTORY_SEPARATOR.'somefile.php';

        self::assertCount(0, $changed);

        $errors = $errorsManager->getInvalidErrors();

        self::assertCount(1, $errors);

        $error = $errors[0];

        self::assertInstanceOf(LintingException::class, $error->getSource());

        self::assertSame(Error::TYPE_INVALID, $error->getType());
        self::assertSame($pathToInvalidFile, $error->getFilePath());
    }

    /**
     * @param list<string> $paths
     *
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixParallel
     * @covers \PhpCsFixer\Runner\Runner::fixSequential
     *
     * @dataProvider provideRunnerUsesProperAnalysisModeCases
     */
    public function testRunnerUsesProperAnalysisMode(
        ParallelConfig $parallelConfig,
        array $paths,
        string $expectedMode
    ): void {
        $runner = new Runner(
            Finder::create()->in($paths),
            [
                new Fixer\ClassNotation\ModifierKeywordsFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ],
            new NullDiffer(),
            $eventDispatcher = new EventDispatcher(),
            new ErrorsManager(),
            new Linter(),
            true,
            new NullCacheManager(),
            null,
            false,
            $parallelConfig,
            new ArrayInput(['--config' => ConfigurationResolver::IGNORE_CONFIG_FILE], (new FixCommand(new ToolInfo()))->getDefinition())
        );

        $eventDispatcher->addListener(AnalysisStarted::NAME, static function (AnalysisStarted $event) use ($expectedMode): void {
            self::assertSame($expectedMode, $event->getMode());
        });

        $runner->fix();
    }

    /**
     * @return iterable<string, array{0: ParallelConfig, 1: list<string>}>
     */
    public static function provideRunnerUsesProperAnalysisModeCases(): iterable
    {
        $fixturesBasePath = realpath(__DIR__.\DIRECTORY_SEPARATOR.'..').\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR;

        yield 'single CPU = sequential even though file chunk is lower than actual files count' => [
            new ParallelConfig(1, 1),
            [$fixturesBasePath.'fix'],
            'sequential',
        ];

        yield 'less files to fix than configured file chunk = sequential even though multiple CPUs enabled' => [
            new ParallelConfig(5, 10),
            [$fixturesBasePath.'fix'],
            'sequential',
        ];

        yield 'multiple CPUs, more files to fix than file chunk size = parallel' => [
            new ParallelConfig(2, 1),
            [$fixturesBasePath.'fix'],
            'parallel',
        ];
    }

    /**
     * @requires OS Darwin|Windows
     *
     * @TODO v4 do not switch on parallel execution by default while this test is not passing on Linux.
     *
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     * @covers \PhpCsFixer\Runner\Runner::fixParallel
     *
     * @dataProvider provideParallelFixStopsOnFirstViolationIfSuchOptionIsEnabledCases
     */
    public function testParallelFixStopsOnFirstViolationIfSuchOptionIsEnabled(bool $stopOnViolation, int $expectedChanges): void
    {
        $errorsManager = new ErrorsManager();

        $path = realpath(__DIR__.\DIRECTORY_SEPARATOR.'..').\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'fix';
        $runner = new Runner(
            Finder::create()->in($path),
            [
                new Fixer\ClassNotation\ModifierKeywordsFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ],
            new NullDiffer(),
            null,
            $errorsManager,
            new Linter(),
            true,
            new NullCacheManager(),
            null,
            $stopOnViolation,
            new ParallelConfig(2, 1, 3),
            new ArrayInput(['--config' => ConfigurationResolver::IGNORE_CONFIG_FILE], (new FixCommand(new ToolInfo()))->getDefinition())
        );

        self::assertCount($expectedChanges, $runner->fix());
    }

    /**
     * @return iterable<string, array{0: bool, 1: int}>
     */
    public static function provideParallelFixStopsOnFirstViolationIfSuchOptionIsEnabledCases(): iterable
    {
        yield 'do NOT stop on violation' => [false, 2];

        yield 'stop on violation' => [true, 1];
    }

    /**
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatDiffedFileIsPassedToDiffer(): void
    {
        $differ = $this->createDifferDouble();
        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'fix';
        $fixers = [
            new Fixer\ClassNotation\ModifierKeywordsFixer(),
        ];

        $runner = new Runner(
            Finder::create()->in($path),
            $fixers,
            $differ,
            null,
            new ErrorsManager(),
            new Linter(),
            true,
            new NullCacheManager(),
            new Directory($path),
            true
        );

        $runner->fix();

        self::assertSame(
            $path,
            \Closure::bind(
                static fn ($differ): string => $differ->passedFile->getPath(),
                null,
                \get_class($differ)
            )($differ),
        );
    }

    /**
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     */
    public function testRuleCustomisationPolicy(): void
    {
        $path = \dirname(__DIR__).\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'rule-customisation';

        $arraySyntaxFixer = new Fixer\ArrayNotation\ArraySyntaxFixer();
        $arraySyntaxFixer->configure(['syntax' => 'short']);

        $fixWith = static function (ErrorsManager $errorsManager, RuleCustomisationPolicyInterface $policy) use ($path, $arraySyntaxFixer): array {
            $runner = new Runner(
                // $fileIterator
                Finder::create()->in($path),
                // $fixers
                [$arraySyntaxFixer],
                // $differ
                new NullDiffer(),
                // $eventDispatcher
                null,
                // $errorsManager
                $errorsManager,
                // $linter
                new Linter(),
                // $isDryRun
                true,
                // $cacheManager
                new NullCacheManager(),
                // $directory
                new Directory($path),
                // $stopOnViolation
                false,
                // $parallelConfig
                null,
                // $input
                null,
                // $configFile
                null,
                // $ruleCustomisationPolicy
                $policy
            );
            $fixInfo = $runner->fix();
            $fixedFiles = array_keys($fixInfo);
            sort($fixedFiles, \SORT_STRING);

            return $fixedFiles;
        };

        // Test when the policy doesn't change a fixer
        $errorsManager = new ErrorsManager();
        $fixedFiles = $fixWith(
            $errorsManager,
            new class implements RuleCustomisationPolicyInterface {
                public function policyVersionForCache(): string
                {
                    return 'test#no changes';
                }

                public function getRuleCustomisers(): array
                {
                    return [
                        'array_syntax' => static fn (\SplFileInfo $file) => true,
                    ];
                }
            }
        );
        self::assertTrue($errorsManager->isEmpty(), 'No errors should occur in the fix() method');
        self::assertSame(['A.php', 'B.php', 'C.php'], $fixedFiles, 'A: fixed, B: fixed, C: fixed, D: already ok');

        // Test when the policy disables a fixer for a specific file
        $errorsManager = new ErrorsManager();
        $fixedFiles = $fixWith(
            $errorsManager,
            new class implements RuleCustomisationPolicyInterface {
                public function policyVersionForCache(): string
                {
                    return 'test#disable one';
                }

                public function getRuleCustomisers(): array
                {
                    return [
                        'array_syntax' => static fn (\SplFileInfo $file) => 'B.php' === $file->getBasename() ? false : true,
                    ];
                }
            }
        );
        self::assertTrue($errorsManager->isEmpty(), 'No errors should occur in the fix() method');
        self::assertSame(['A.php', 'C.php'], $fixedFiles, 'A: fixed, B: skipped, C: fixed, D: already ok');

        // Test when the policy changes a fixer for specific files
        $errorsManager = new ErrorsManager();
        $fixedFiles = $fixWith(
            $errorsManager,
            new class implements RuleCustomisationPolicyInterface {
                public function policyVersionForCache(): string
                {
                    return 'test#reconfigure';
                }

                public function getRuleCustomisers(): array
                {
                    return [
                        'array_syntax' => static function (\SplFileInfo $file) {
                            if (\in_array($file->getBasename(), ['B.php', 'D.php'], true)) {
                                $fixer = new Fixer\ArrayNotation\ArraySyntaxFixer();
                                $fixer->configure(['syntax' => 'long']);

                                return $fixer;
                            }

                            return true;
                        },
                    ];
                }
            }
        );
        self::assertTrue($errorsManager->isEmpty(), 'No errors should occur in the fix() method');
        self::assertSame(['A.php', 'C.php', 'D.php'], $fixedFiles, 'A: fixed, B: ok for new configuration, C: fixed, D: fixed with new configuration');

        // Test when the policy changes the fixer class - that's not allowed
        $errorsManager = new ErrorsManager();
        $fixedFiles = $fixWith(
            $errorsManager,
            new class implements RuleCustomisationPolicyInterface {
                public function policyVersionForCache(): string
                {
                    return 'test#wrong fixer class';
                }

                public function getRuleCustomisers(): array
                {
                    return [
                        'array_syntax' => static fn (\SplFileInfo $file) => 'B.php' === $file->getBasename() ? new Fixer\Whitespace\LineEndingFixer() : true,
                    ];
                }
            }
        );
        self::assertFalse($errorsManager->isEmpty(), 'An error should occur when the policy changes the fixer class for file B');
        $errorsForB = $errorsManager->forPath($path.\DIRECTORY_SEPARATOR.'B.php');
        self::assertCount(1, $errorsForB, 'An error should occur when the policy changes the fixer class for file B');
        self::assertInstanceOf(\RuntimeException::class, $errorsForB[0]->getSource());
        self::assertStringContainsString('expected '.\get_class($arraySyntaxFixer), $errorsForB[0]->getSource()->getMessage());
        self::assertStringContainsString('got '.Fixer\Whitespace\LineEndingFixer::class, $errorsForB[0]->getSource()->getMessage());
        self::assertSame(['A.php', 'C.php'], $fixedFiles, 'A: fixed, B: exception thrown, C: fixed, D: already ok');

        // Test when the policy contains customisers for non-existing fixers
        $errorsManager = new ErrorsManager();
        self::expectException(\RuntimeException::class);
        // The exception message should mention line_ending
        self::expectExceptionMessageMatches('/\bline_ending\b/');
        $fixedFiles = $fixWith(
            $errorsManager,
            new class implements RuleCustomisationPolicyInterface {
                public function policyVersionForCache(): string
                {
                    return 'test#missing fixer';
                }

                public function getRuleCustomisers(): array
                {
                    return [
                        'array_syntax' => static fn (\SplFileInfo $file) => true,
                        'line_ending' => static fn (\SplFileInfo $file) => true,
                    ];
                }
            }
        );
    }

    private function createDifferDouble(): DifferInterface
    {
        return new class implements DifferInterface {
            public ?\SplFileInfo $passedFile = null;

            public function diff(string $old, string $new, ?\SplFileInfo $file = null): string
            {
                $this->passedFile = $file;

                return 'some-diff';
            }
        };
    }

    private function createLinterDouble(): LinterInterface
    {
        return new class implements LinterInterface {
            public function isAsync(): bool
            {
                return false;
            }

            public function lintFile(string $path): LintingResultInterface
            {
                return new class implements LintingResultInterface {
                    public function check(): void {}
                };
            }

            public function lintSource(string $source): LintingResultInterface
            {
                return new class implements LintingResultInterface {
                    public function check(): void {}
                };
            }
        };
    }
}
