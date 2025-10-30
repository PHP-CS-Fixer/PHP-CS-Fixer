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
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\NullDiffer;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Fixer;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\RuleCustomizationPolicyInterface;
use PhpCsFixer\Runner\Event\AnalysisStarted;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\ToolInfo;
use PHPUnit\Framework\Assert;
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
            new ArrayInput([], (new FixCommand(new ToolInfo()))->getDefinition())
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
            new ArrayInput([], (new FixCommand(new ToolInfo()))->getDefinition())
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
            new ArrayInput([], (new FixCommand(new ToolInfo()))->getDefinition())
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
    public function testRuleCustomizationPolicy(): void
    {
        $differ = $this->createDifferDouble();
        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'fix';
        $ruleCustomizationPolicy = new class implements RuleCustomizationPolicyInterface {
            public Fixer\FixerInterface $fixer1;
            public Fixer\FixerInterface $fixer2;
            public Fixer\FixerInterface $fixer3;

            public function customize(Fixer\FixerInterface $fixer, \SplFileInfo $file): ?Fixer\FixerInterface
            {
                if ($fixer === $this->fixer1) {
                    return null;
                }
                if ($fixer === $this->fixer2) {
                    return $this->fixer3;
                }
                Assert::fail('Unexpected fixer passed to filter.');
            }
        };
        $ruleCustomizationPolicy->fixer1 = new class implements Fixer\FixerInterface {
            public function isCandidate(Tokens $tokens): bool
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function isRisky(): bool
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function getName(): string
            {
                return 'fixer1';
            }

            public function getPriority(): int
            {
                return 0;
            }

            public function supports(\SplFileInfo $file): bool
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }
        };
        $ruleCustomizationPolicy->fixer2 = new class implements Fixer\FixerInterface {
            public function isCandidate(Tokens $tokens): bool
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function isRisky(): bool
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function getName(): string
            {
                return 'fixer2';
            }

            public function getPriority(): int
            {
                return 0;
            }

            public function supports(\SplFileInfo $file): bool
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }
        };
        $ruleCustomizationPolicy->fixer3 = new class implements Fixer\FixerInterface {
            public bool $fixInvoked = false;

            public function isCandidate(Tokens $tokens): bool
            {
                return true;
            }

            public function isRisky(): bool
            {
                return false;
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                $this->fixInvoked = true;
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                Assert::fail(__FUNCTION__." shoudn't be called on this fixer");
            }

            public function getName(): string
            {
                return 'fixer3';
            }

            public function getPriority(): int
            {
                return 0;
            }

            public function supports(\SplFileInfo $file): bool
            {
                return true;
            }
        };

        $errorsManager = new ErrorsManager();

        $runner = new Runner(
            // $fileIterator
            Finder::create()->in($path),
            // $fixers
            [
                $ruleCustomizationPolicy->fixer1,
                $ruleCustomizationPolicy->fixer2,
            ],
            // $differ
            $differ,
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
            true,
            // $parallelConfig
            null,
            // $input
            null,
            // $configFile
            null,
            // $ruleCustomizationPolicy
            $ruleCustomizationPolicy
        );

        $fixInfo = $runner->fix();

        self::assertTrue($errorsManager->isEmpty(), 'No errors should occur in the fix() method');
        self::assertSame([], $fixInfo);
        self::assertTrue($ruleCustomizationPolicy->fixer3->fixInvoked, 'The fix method on fixer3 should be invoked');
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
