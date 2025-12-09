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
use PhpCsFixer\Fixer\FixerInterface;
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
 * @phpstan-import-type _RuleCustomisationPolicyCallback from RuleCustomisationPolicyInterface
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
     * @param non-empty-string                                                  $path
     * @param _RuleCustomisationPolicyCallback                                  $arraySyntaxCustomiser
     * @param ?list<array{type: int, filePath: string, sourceMessage: ?string}> $expectedErrors
     * @param list<string>                                                      $expectedFixedFiles
     *
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     *
     * @dataProvider provideRuleCustomisationPolicyCases
     */
    public function testRuleCustomisationPolicy(string $path, \Closure $arraySyntaxCustomiser, ?array $expectedErrors, array $expectedFixedFiles): void
    {
        $arraySyntaxFixer = new Fixer\ArrayNotation\ArraySyntaxFixer();
        $arraySyntaxFixer->configure(['syntax' => 'short']);

        $policy = new class implements RuleCustomisationPolicyInterface {
            public \Closure $arraySyntaxCustomiser;

            public function getPolicyVersionForCache(): string
            {
                return '';
            }

            public function getRuleCustomisers(): array
            {
                return [
                    'array_syntax' => fn (\SplFileInfo $file) => ($this->arraySyntaxCustomiser)($file),
                ];
            }
        };
        $policy->arraySyntaxCustomiser = $arraySyntaxCustomiser;
        $errorsManager = new ErrorsManager();

        $fixedFiles = self::runRunnerWithPolicy($path, [$arraySyntaxFixer], $policy, $errorsManager);
        self::assertSame($expectedFixedFiles, $fixedFiles);

        $actualErrors = array_map(
            static fn (Error $error): array => [
                'type' => $error->getType(),
                'filePath' => $error->getFilePath(),
                'sourceMessage' => null === $error->getSource() ? null : $error->getSource()->getMessage(),
            ],
            $errorsManager->getAllErrors()
        );

        self::assertEqualsCanonicalizing(
            $expectedErrors ?? [],
            $actualErrors,
            'Errors do not match expected.'
        );
    }

    /**
     * @return iterable<string, array{non-empty-string, _RuleCustomisationPolicyCallback, ?list<array{type: int, filePath: string, sourceMessage: ?string}>, list<string>}>
     */
    public static function provideRuleCustomisationPolicyCases(): iterable
    {
        $path = \dirname(__DIR__).\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'rule-customisation';

        yield "Test when the policy doesn't change a fixer" => [
            $path,
            static fn (\SplFileInfo $file) => true,
            null,
            // A: fixed, B: fixed, C: fixed, D: already ok
            ['A.php', 'B.php', 'C.php'],
        ];

        yield 'Test when the policy disables a fixer for a specific file' => [
            $path,
            static fn (\SplFileInfo $file) => 'B.php' === $file->getBasename() ? false : true,
            null,
            // A: fixed, B: skipped, C: fixed, D: already ok
            ['A.php', 'C.php'],
        ];

        yield 'Test when the policy changes a fixer for specific files' => [
            $path,
            static function (\SplFileInfo $file) {
                if (\in_array($file->getBasename(), ['B.php', 'D.php'], true)) {
                    $fixer = new Fixer\ArrayNotation\ArraySyntaxFixer();
                    $fixer->configure(['syntax' => 'long']);

                    return $fixer;
                }

                return true;
            },
            null,
            // A: fixed, B: ok for new configuration, C: fixed, D: fixed with new configuration
            ['A.php', 'C.php', 'D.php'],
        ];

        yield 'Test when the policy changes the fixer class (not allowed)' => [
            $path,
            static fn (\SplFileInfo $file) => 'B.php' === $file->getBasename() ? new Fixer\Whitespace\LineEndingFixer() : true,
            [
                [
                    'type' => Error::TYPE_EXCEPTION,
                    'filePath' => $path.\DIRECTORY_SEPARATOR.'B.php',
                    'sourceMessage' => \sprintf(
                        'The fixer returned by the Rule Customisation Policy must be of the same class as the original fixer (expected `%s`, got `%s`).',
                        Fixer\ArrayNotation\ArraySyntaxFixer::class,
                        Fixer\Whitespace\LineEndingFixer::class,
                    ),
                ],
            ],
            // A: fixed, B: exception thrown, C: fixed, D: already ok
            ['A.php', 'C.php'],
        ];
    }

    public function testRuleCustomisationPolicyWithMissingFixers(): void
    {
        $policy = new class implements RuleCustomisationPolicyInterface {
            public function getPolicyVersionForCache(): string
            {
                return '';
            }

            public function getRuleCustomisers(): array
            {
                return [
                    'array_syntax' => static fn (\SplFileInfo $file) => true,
                    'line_ending' => static fn (\SplFileInfo $file) => true,
                ];
            }
        };

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessageMatches('/\bline_ending\b/');
        self::runRunnerWithPolicy(__DIR__, [new Fixer\ArrayNotation\ArraySyntaxFixer()], $policy, new ErrorsManager());
    }

    /**
     * @dataProvider provideRuleCustomisationPolicyWithWrongCustomisersCases
     */
    public function testRuleCustomisationPolicyWithWrongCustomisers(array $customisers, string $error): void
    {
        $policy = new class($customisers) implements RuleCustomisationPolicyInterface {
            private $customisers;

            public function __construct($customisers)
            {
                $this->customisers = $customisers;
            }

            public function getRuleCustomisationPolicy(): string
            {
                return '';
            }

            public function getRuleCustomisers(): array
            {
                return $this->customisers;
            }
        };

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessageMatches($error);
        self::runRunnerWithPolicy(__DIR__, [new Fixer\ArrayNotation\ArraySyntaxFixer()], $policy, new ErrorsManager());
    }

    /**
     * @return iterable<string, array{non-empty-string, _RuleCustomizationPolicyCallback, ?list<array{type: int, filePath: string, sourceMessage: ?string}>, list<string>}>
     */
    public static function provideRuleCustomisationPolicyWithWrongCustomisersCases(): iterable
    {
        yield 'empty rule-key' => [
            [
                '' => static fn (\SplFileInfo $file) => true,
            ],
            '/\(no name provided\)/',
        ];

        yield 'set as rule-key' => [
            [
                '@auto' => static fn (\SplFileInfo $file) => true,
            ],
            '/@auto \(can exclude only rules, not sets\)/',
        ];
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

    /**
     * @param non-empty-string     $path
     * @param list<FixerInterface> $fixers
     *
     * @return list<string> the names of the fixed files
     */
    private static function runRunnerWithPolicy(string $path, array $fixers, RuleCustomisationPolicyInterface $policy, ErrorsManager $errorsManager): array
    {
        $runner = new Runner(
            // $fileIterator
            Finder::create()->in($path),
            // $fixers
            $fixers,
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
    }
}
