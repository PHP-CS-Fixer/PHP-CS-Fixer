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

namespace PhpCsFixer\Tests\Console;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Console\Output\Progress\ProgressOutputType;
use PhpCsFixer\Console\Report\FixReport\CheckstyleReporter;
use PhpCsFixer\Console\Report\FixReport\GitHubReporter;
use PhpCsFixer\Console\Report\FixReport\GitlabReporter;
use PhpCsFixer\Console\Report\FixReport\JsonReporter;
use PhpCsFixer\Console\Report\FixReport\TextReporter;
use PhpCsFixer\Differ\NullDiffer;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Tests\Fixtures\ExternalRuleSet\ExampleRuleSet;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\ToolInfoInterface;
use PhpCsFixer\Utils;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\ConfigurationResolver
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ConfigurationResolverTest extends TestCase
{
    public function testResolveParallelConfig(): void
    {
        $parallelConfig = new ParallelConfig();
        $config = (new Config())->setParallelConfig($parallelConfig);
        $resolver = $this->createConfigurationResolver([], $config);

        self::assertSame($parallelConfig, $resolver->getParallelConfig());
    }

    public function testDefaultParallelConfigFallbacksToAutoDetect(): void
    {
        $parallelConfig = $this->createConfigurationResolver([])->getParallelConfig();
        $defaultParallelConfig = ParallelConfigFactory::detect();

        self::assertSame($defaultParallelConfig->getMaxProcesses(), $parallelConfig->getMaxProcesses());
        self::assertSame($defaultParallelConfig->getFilesPerProcess(), $parallelConfig->getFilesPerProcess());
        self::assertSame($defaultParallelConfig->getProcessTimeout(), $parallelConfig->getProcessTimeout());
    }

    public function testCliSequentialOptionOverridesParallelConfig(): void
    {
        $config = (new Config())->setParallelConfig(new ParallelConfig(10));
        $resolver = $this->createConfigurationResolver(['sequential' => true], $config);

        self::assertSame(1, $resolver->getParallelConfig()->getMaxProcesses());
    }

    public function testSetOptionWithUndefinedOption(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^Unknown option name: "foo"\.$/');

        $this->createConfigurationResolver(['foo' => 'bar']);
    }

    public function testResolveProgressWithPositiveConfigAndPositiveOption(): void
    {
        $config = new Config();
        $config->setHideProgress(true);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
        ], $config);

        self::assertSame('none', $resolver->getProgressType());
    }

    public function testResolveProgressWithPositiveConfigAndNegativeOption(): void
    {
        $config = new Config();
        $config->setHideProgress(true);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ], $config);

        self::assertSame('none', $resolver->getProgressType());
    }

    public function testResolveProgressWithNegativeConfigAndPositiveOption(): void
    {
        $config = new Config();
        $config->setHideProgress(false);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
        ], $config);

        self::assertSame('bar', $resolver->getProgressType());
    }

    public function testResolveProgressWithNegativeConfigAndNegativeOption(): void
    {
        $config = new Config();
        $config->setHideProgress(false);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ], $config);

        self::assertSame('bar', $resolver->getProgressType());
    }

    /**
     * @dataProvider provideProgressTypeCases
     */
    public function testResolveProgressWithPositiveConfigAndExplicitProgress(string $progressType): void
    {
        $config = new Config();
        $config->setHideProgress(true);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            'show-progress' => $progressType,
        ], $config);

        self::assertSame($progressType, $resolver->getProgressType());
    }

    /**
     * @dataProvider provideProgressTypeCases
     */
    public function testResolveProgressWithNegativeConfigAndExplicitProgress(string $progressType): void
    {
        $config = new Config();
        $config->setHideProgress(false);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            'show-progress' => $progressType,
        ], $config);

        self::assertSame($progressType, $resolver->getProgressType());
    }

    /**
     * @return iterable<string, array{0: ProgressOutputType::*}>
     */
    public static function provideProgressTypeCases(): iterable
    {
        foreach (ProgressOutputType::all() as $outputType) {
            yield $outputType => [$outputType];
        }
    }

    public function testResolveProgressWithInvalidExplicitProgress(): void
    {
        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            'show-progress' => 'foo',
        ]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The progress type "foo" is not defined, supported are "bar", "dots" and "none".');

        $resolver->getProgressType();
    }

    public function testResolveConfigFileDefault(): void
    {
        $resolver = $this->createConfigurationResolver([]);

        self::assertNull($resolver->getConfigFile());
    }

    public function testResolveConfigFileByPathOfFile(): void
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_1';

        $resolver = $this->createConfigurationResolver(['path' => [$dir.\DIRECTORY_SEPARATOR.'foo.php']]);

        self::assertSame($dir.\DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php', $resolver->getConfigFile());
        self::assertInstanceOf(\Test1Config::class, $resolver->getConfig()); // @phpstan-ignore-line to avoid `Class Test1Config not found.`
    }

    public function testResolveConfigFileSpecified(): void
    {
        $file = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_4/my.php-cs-fixer.php';

        $resolver = $this->createConfigurationResolver(['config' => $file]);

        self::assertSame($file, $resolver->getConfigFile());
        self::assertInstanceOf(\Test4Config::class, $resolver->getConfig()); // @phpstan-ignore-line to avoid `Class Test4Config not found.`
    }

    /**
     * @dataProvider provideResolveConfigFileChooseFileCases
     *
     * @param class-string<ConfigInterface> $expectedClass
     */
    public function testResolveConfigFileChooseFile(string $expectedFile, string $expectedClass, string $path, ?string $cwdPath = null): void
    {
        $resolver = $this->createConfigurationResolver(
            ['path' => [$path]],
            null,
            $cwdPath ?? '',
        );

        self::assertSame($expectedFile, $resolver->getConfigFile());
        self::assertInstanceOf($expectedClass, $resolver->getConfig());
    }

    /**
     * @return iterable<int, array{0: string, 1: string, 2: string, 3?: string}>
     */
    public static function provideResolveConfigFileChooseFileCases(): iterable
    {
        $dirBase = self::getFixtureDir();

        yield [
            $dirBase.'case_1'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php',
            'Test1Config',
            $dirBase.'case_1',
        ];

        yield [
            $dirBase.'case_2'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.php',
            'Test2Config',
            $dirBase.'case_2',
        ];

        yield [
            $dirBase.'case_3'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.php',
            'Test3Config',
            $dirBase.'case_3',
        ];

        yield [
            $dirBase.'case_6'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php',
            'Test6Config',
            $dirBase.'case_6'.\DIRECTORY_SEPARATOR.'subdir',
            $dirBase.'case_6',
        ];

        yield [
            $dirBase.'case_6'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php',
            'Test6Config',
            $dirBase.'case_6'.\DIRECTORY_SEPARATOR.'subdir/empty_file.php',
            $dirBase.'case_6',
        ];
    }

    public function testResolveConfigFileChooseFileWithInvalidFile(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '#^The config file: ".+[\/\\\]Fixtures[\/\\\]ConfigurationResolverConfigFile[\/\\\]case_5[\/\\\]\.php-cs-fixer\.dist\.php" does not return a "PhpCsFixer\\\ConfigInterface" instance\. Got: "string"\.$#',
        );

        $dirBase = self::getFixtureDir();

        $resolver = $this->createConfigurationResolver(['path' => [$dirBase.'case_5']]);

        $resolver->getConfig();
    }

    public function testResolveConfigFileChooseFileWithInvalidFormat(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^The format "xls" is not defined, supported are "checkstyle", "github", "gitlab", "json", "junit", "txt" and "xml"\.$/');

        $dirBase = self::getFixtureDir();

        $resolver = $this->createConfigurationResolver(['path' => [$dirBase.'case_7']]);

        $resolver->getReporter();
    }

    public function testResolveConfigFileChooseFileWithPathArrayWithoutConfig(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^For multiple paths config parameter is required\.$/');

        $dirBase = self::getFixtureDir();

        $resolver = $this->createConfigurationResolver(['path' => [$dirBase.'case_1/.php-cs-fixer.dist.php', $dirBase.'case_1/foo.php']]);

        $resolver->getConfig();
    }

    public function testResolveConfigFileChooseFileWithPathArrayAndConfig(): void
    {
        $dirBase = self::getFixtureDir();
        $configFile = $dirBase.'case_1/.php-cs-fixer.dist.php';

        $resolver = $this->createConfigurationResolver([
            'config' => $configFile,
            'path' => [$configFile, $dirBase.'case_1/foo.php'],
        ]);

        self::assertSame($configFile, $resolver->getConfigFile());
    }

    /**
     * @param array<int, string> $paths
     * @param array<int, string> $expectedPaths
     *
     * @dataProvider provideResolvePathCases
     */
    public function testResolvePath(array $paths, string $cwd, array $expectedPaths): void
    {
        $resolver = $this->createConfigurationResolver(
            ['path' => $paths],
            null,
            $cwd,
        );

        self::assertSame($expectedPaths, $resolver->getPath());
    }

    /**
     * @return iterable<int, array{array<int, string>, string, array<int, string>}>
     */
    public static function provideResolvePathCases(): iterable
    {
        yield [
            ['Command'],
            __DIR__,
            [__DIR__.\DIRECTORY_SEPARATOR.'Command'],
        ];

        yield [
            [basename(__DIR__)],
            \dirname(__DIR__),
            [__DIR__],
        ];

        yield [
            [' Command'],
            __DIR__,
            [__DIR__.\DIRECTORY_SEPARATOR.'Command'],
        ];

        yield [
            ['Command '],
            __DIR__,
            [__DIR__.\DIRECTORY_SEPARATOR.'Command'],
        ];
    }

    /**
     * @param list<string> $paths
     *
     * @dataProvider provideRejectInvalidPathCases
     */
    public function testRejectInvalidPath(array $paths, string $expectedMessage): void
    {
        $resolver = $this->createConfigurationResolver(
            ['path' => $paths],
            null,
            \dirname(__DIR__),
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($expectedMessage);

        $resolver->getPath();
    }

    /**
     * @return iterable<int, array{list<string>, string}>
     */
    public static function provideRejectInvalidPathCases(): iterable
    {
        yield [
            [''],
            'Invalid path: "".',
        ];

        yield [
            [__DIR__, ''],
            'Invalid path: "".',
        ];

        yield [
            ['', __DIR__],
            'Invalid path: "".',
        ];

        yield [
            ['  '],
            'Invalid path: "  ".',
        ];

        yield [
            [__DIR__, '  '],
            'Invalid path: "  ".',
        ];

        yield [
            ['  ', __DIR__],
            'Invalid path: "  ".',
        ];
    }

    public function testResolvePathWithFileThatIsExcludedDirectlyOverridePathMode(): void
    {
        $config = new Config();
        $finder = $config->getFinder();

        \assert($finder instanceof Finder); // Config::getFinder() ensures only `iterable`

        $finder
            ->in(__DIR__.'/../Fixtures')
            ->notPath('dummy-file.php')
        ;

        $resolver = $this->createConfigurationResolver(
            ['path' => [__DIR__.'/../Fixtures/dummy-file.php']],
            $config,
        );

        self::assertCount(1, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedDirectlyIntersectionPathMode(): void
    {
        $config = new Config();
        $finder = $config->getFinder();

        \assert($finder instanceof Finder); // Config::getFinder() ensures only `iterable`

        $finder
            ->in(__DIR__.'/../Fixtures')
            ->notPath('dummy-file.php')
        ;

        $resolver = $this->createConfigurationResolver([
            'path' => [__DIR__.'/../Fixtures/dummy-file.php'],
            'path-mode' => 'intersection',
        ], $config);

        self::assertCount(0, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedByDirOverridePathMode(): void
    {
        $dir = __DIR__.'/..';
        $config = new Config();
        $finder = $config->getFinder();

        \assert($finder instanceof Finder); // Config::getFinder() ensures only `iterable`

        $finder
            ->in($dir)
            ->exclude('Fixtures')
        ;

        $resolver = $this->createConfigurationResolver(
            ['path' => [__DIR__.'/../Fixtures/dummy-file.php']],
            $config,
        );

        self::assertCount(1, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedByDirIntersectionPathMode(): void
    {
        $dir = __DIR__.'/..';
        $config = new Config();
        $finder = $config->getFinder();

        \assert($finder instanceof Finder); // Config::getFinder() ensures only `iterable`

        $finder
            ->in($dir)
            ->exclude('Fixtures')
        ;

        $resolver = $this->createConfigurationResolver([
            'path-mode' => 'intersection',
            'path' => [__DIR__.'/../Fixtures/dummy-file.php'],
        ], $config);

        self::assertCount(0, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsNotExcluded(): void
    {
        $dir = __DIR__;
        $config = new Config();
        $finder = $config->getFinder();

        \assert($finder instanceof Finder); // Config::getFinder() ensures only `iterable`

        $finder
            ->in($dir)
            ->notPath('foo-dummy-file.php')
        ;

        $resolver = $this->createConfigurationResolver(
            ['path' => [__DIR__.'/../Fixtures/dummy-file.php']],
            $config,
        );

        self::assertCount(1, $resolver->getFinder());
    }

    /**
     * @param \Exception|list<string> $expected
     * @param list<string>            $path
     *
     * @dataProvider provideResolveIntersectionOfPathsCases
     */
    public function testResolveIntersectionOfPaths($expected, ?Finder $configFinder, array $path, string $pathMode, ?string $configOption = null): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(\get_class($expected));
        }

        if (null !== $configFinder) {
            $config = new Config();
            $config->setFinder($configFinder);
        } else {
            $config = null;
        }

        $resolver = $this->createConfigurationResolver([
            'config' => $configOption,
            'path' => $path,
            'path-mode' => $pathMode,
        ], $config);

        $intersectionItems = array_map(
            static fn (\SplFileInfo $file): string => $file->getRealPath(),
            iterator_to_array($resolver->getFinder(), false),
        );

        self::assertIsArray($expected);

        sort($expected);
        sort($intersectionItems);

        self::assertSame($expected, $intersectionItems);
    }

    /**
     * @return iterable<string, array{0: array<array-key, string>|\Exception, 1: null|Finder, 2: list<string>, 3: string, 4?: string}>
     */
    public static function provideResolveIntersectionOfPathsCases(): iterable
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverPathsIntersection';
        $cb = static fn (array $items): array => array_map(
            static fn (string $item): string => (string) realpath($dir.'/'.$item),
            $items,
        );

        yield 'no path at all' => [
            new \LogicException(),
            Finder::create(),
            [],
            'override',
        ];

        yield 'configured only by finder' => [
            // don't override if the argument is empty
            $cb(['a1.php', 'a2.php', 'b/b1.php', 'b/b2.php', 'b_b/b_b1.php', 'c/c1.php', 'c/d/cd1.php', 'd/d1.php', 'd/d2.php', 'd/e/de1.php', 'd/f/df1.php']),
            Finder::create()
                ->in($dir),
            [],
            'override',
        ];

        yield 'configured only by argument' => [
            $cb(['a1.php', 'a2.php', 'b/b1.php', 'b/b2.php', 'b_b/b_b1.php', 'c/c1.php', 'c/d/cd1.php', 'd/d1.php', 'd/d2.php', 'd/e/de1.php', 'd/f/df1.php']),
            Finder::create(),
            [$dir],
            'override',
        ];

        yield 'configured by finder, intersected with empty argument' => [
            [],
            Finder::create()
                ->in($dir),
            [],
            'intersection',
        ];

        yield 'configured by finder, intersected with dir' => [
            $cb(['c/c1.php', 'c/d/cd1.php']),
            Finder::create()
                ->in($dir),
            [$dir.'/c'],
            'intersection',
        ];

        yield 'configured by finder, intersected with file' => [
            $cb(['c/c1.php']),
            Finder::create()
                ->in($dir),
            [$dir.'/c/c1.php'],
            'intersection',
        ];

        yield 'finder points to one dir while argument to another, not connected' => [
            [],
            Finder::create()
                ->in($dir.'/b'),
            [$dir.'/c'],
            'intersection',
        ];

        yield 'finder with excluded dir, intersected with excluded file' => [
            [],
            Finder::create()
                ->in($dir)
                ->exclude('c'),
            [$dir.'/c/d/cd1.php'],
            'intersection',
        ];

        yield 'finder with excluded dir, intersected with dir containing excluded one' => [
            $cb(['c/c1.php']),
            Finder::create()
                ->in($dir)
                ->exclude('c/d'),
            [$dir.'/c'],
            'intersection',
        ];

        yield 'finder with excluded file, intersected with dir containing excluded one' => [
            $cb(['c/d/cd1.php']),
            Finder::create()
                ->in($dir)
                ->notPath('c/c1.php'),
            [$dir.'/c'],
            'intersection',
        ];

        yield 'configured by finder, intersected with non-existing path' => [
            new \LogicException(),
            Finder::create()
                ->in($dir),
            ['non_existing_dir'],
            'intersection',
        ];

        yield 'configured by config file, overridden by multiple files' => [
            $cb(['d/d1.php', 'd/d2.php']),
            null,
            [$dir.'/d/d1.php', $dir.'/d/d2.php'],
            'override',
            $dir.'/d/.php-cs-fixer.php',
        ];

        yield 'configured by config file, intersected with multiple files' => [
            $cb(['d/d1.php', 'd/d2.php']),
            null,
            [$dir.'/d/d1.php', $dir.'/d/d2.php'],
            'intersection',
            $dir.'/d/.php-cs-fixer.php',
        ];

        yield 'configured by config file, overridden by non-existing dir' => [
            new \LogicException(),
            null,
            [$dir.'/d/fff'],
            'override',
            $dir.'/d/.php-cs-fixer.php',
        ];

        yield 'configured by config file, intersected with non-existing dir' => [
            new \LogicException(),
            null,
            [$dir.'/d/fff'],
            'intersection',
            $dir.'/d/.php-cs-fixer.php',
        ];

        yield 'configured by config file, overridden by non-existing file' => [
            new \LogicException(),
            null,
            [$dir.'/d/fff.php'],
            'override',
            $dir.'/d/.php-cs-fixer.php',
        ];

        yield 'configured by config file, intersected with non-existing file' => [
            new \LogicException(),
            null,
            [$dir.'/d/fff.php'],
            'intersection',
            $dir.'/d/.php-cs-fixer.php',
        ];

        yield 'configured by config file, overridden by multiple files and dirs' => [
            $cb(['d/d1.php', 'd/e/de1.php', 'd/f/df1.php']),
            null,
            [$dir.'/d/d1.php', $dir.'/d/e', $dir.'/d/f/'],
            'override',
            $dir.'/d/.php-cs-fixer.php',
        ];

        yield 'configured by config file, intersected with multiple files and dirs' => [
            $cb(['d/d1.php', 'd/e/de1.php', 'd/f/df1.php']),
            null,
            [$dir.'/d/d1.php', $dir.'/d/e', $dir.'/d/f/'],
            'intersection',
            $dir.'/d/.php-cs-fixer.php',
        ];
    }

    /**
     * @param array<string, mixed> $options
     *
     * @dataProvider provideConfigFinderIsOverriddenCases
     */
    public function testConfigFinderIsOverridden(array $options, bool $expectedResult): void
    {
        $resolver = $this->createConfigurationResolver($options);

        self::assertSame($expectedResult, $resolver->configFinderIsOverridden());

        $resolver = $this->createConfigurationResolver($options);
        $resolver->getFinder();

        self::assertSame($expectedResult, $resolver->configFinderIsOverridden());
    }

    /**
     * @return iterable<int, array{array<string, mixed>, bool}>
     */
    public static function provideConfigFinderIsOverriddenCases(): iterable
    {
        $root = __DIR__.'/../..';

        yield [
            [
                'config' => __DIR__.'/../Fixtures/.php-cs-fixer.vanilla.php',
            ],
            false,
        ];

        yield [
            [
                'config' => __DIR__.'/../Fixtures/.php-cs-fixer.vanilla.php',
                'path' => [$root.'/src'],
            ],
            true,
        ];

        yield [
            [
                'config' => ConfigurationResolver::IGNORE_CONFIG_FILE,
            ],
            false,
        ];

        yield [
            [
                'config' => ConfigurationResolver::IGNORE_CONFIG_FILE,
                'path' => [$root.'/src'],
            ],
            false,
        ];

        yield [
            [
                'config' => __DIR__.'/../Fixtures/.php-cs-fixer.vanilla.php',

                'path' => [$root.'/src'],
                'path-mode' => ConfigurationResolver::PATH_MODE_INTERSECTION,
            ],
            false,
        ];

        // scenario when loaded config is not setting custom finder
        yield [
            [
                'config' => $root.'/tests/Fixtures/ConfigurationResolverConfigFile/case_3/.php-cs-fixer.dist.php',
                'path' => [$root.'/src'],
            ],
            false,
        ];

        // scenario when loaded config contains not fully defined finder
        yield [
            [
                'config' => $root.'/tests/Fixtures/ConfigurationResolverConfigFile/case_9/.php-cs-fixer.php',
                'path' => [$root.'/src'],
            ],
            false,
        ];
    }

    public function testResolveIsDryRunViaStdIn(): void
    {
        $resolver = $this->createConfigurationResolver([
            'dry-run' => false,
            'path' => ['-'],
        ]);

        self::assertTrue($resolver->isDryRun());
    }

    public function testResolveIsDryRunViaNegativeOption(): void
    {
        $resolver = $this->createConfigurationResolver(['dry-run' => false]);

        self::assertFalse($resolver->isDryRun());
    }

    public function testResolveIsDryRunViaPositiveOption(): void
    {
        $resolver = $this->createConfigurationResolver(['dry-run' => true]);

        self::assertTrue($resolver->isDryRun());
    }

    /**
     * @dataProvider provideResolveBooleanOptionCases
     */
    public function testResolveUsingCacheWithConfigOption(bool $expected, bool $configValue, ?string $passed): void
    {
        $config = new Config();
        $config->setUsingCache($configValue);

        $resolver = $this->createConfigurationResolver(
            ['using-cache' => $passed],
            $config,
        );

        self::assertSame($expected, $resolver->getUsingCache());
    }

    /**
     * @return iterable<int, array{bool, bool, null|string}>
     */
    public static function provideResolveBooleanOptionCases(): iterable
    {
        yield [true, true, 'yes'];

        yield [true, false, 'yes'];

        yield [false, true, 'no'];

        yield [false, false, 'no'];

        yield [true, true, null];

        yield [false, false, null];
    }

    public function testResolveUsingCacheWithPositiveConfigAndNoOption(): void
    {
        $config = new Config();
        $config->setUsingCache(true);

        $resolver = $this->createConfigurationResolver(
            [],
            $config,
        );

        self::assertTrue($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndNoOption(): void
    {
        $config = new Config();
        $config->setUsingCache(false);

        $resolver = $this->createConfigurationResolver(
            [],
            $config,
        );

        self::assertFalse($resolver->getUsingCache());
    }

    /**
     * @dataProvider provideResolveUsingCacheForRuntimesCases
     */
    public function testResolveUsingCacheForRuntimes(bool $cacheAllowed, bool $installedWithComposer, bool $asPhar, bool $inDocker): void
    {
        $config = new Config();
        $config->setUsingCache(true);

        $resolver = $this->createConfigurationResolver(
            [],
            $config,
            '',
            new class($installedWithComposer, $asPhar, $inDocker) implements ToolInfoInterface {
                private bool $installedWithComposer;
                private bool $asPhar;
                private bool $inDocker;

                public function __construct(bool $installedWithComposer, bool $asPhar, bool $inDocker)
                {
                    $this->installedWithComposer = $installedWithComposer;
                    $this->asPhar = $asPhar;
                    $this->inDocker = $inDocker;
                }

                public function getComposerInstallationDetails(): array
                {
                    throw new \BadMethodCallException();
                }

                public function getComposerVersion(): string
                {
                    throw new \BadMethodCallException();
                }

                public function getVersion(): string
                {
                    throw new \BadMethodCallException();
                }

                public function isInstalledAsPhar(): bool
                {
                    return $this->asPhar;
                }

                public function isInstalledByComposer(): bool
                {
                    return $this->installedWithComposer;
                }

                public function isRunInsideDocker(): bool
                {
                    return $this->inDocker;
                }

                public function getPharDownloadUri(string $version): string
                {
                    throw new \BadMethodCallException();
                }
            },
        );

        self::assertSame($cacheAllowed, $resolver->getUsingCache());
    }

    /**
     * @return iterable<string, array{0: bool, 1: bool, 2: bool, 3: bool}>
     */
    public static function provideResolveUsingCacheForRuntimesCases(): iterable
    {
        yield 'none of the allowed runtimes' => [false, false, false, false];

        yield 'composer installation' => [true, true, false, false];

        yield 'PHAR distribution' => [true, false, true, false];

        yield 'Docker runtime' => [true, false, false, true];
    }

    public function testResolveCacheFileWithoutConfigAndOption(): void
    {
        $config = new Config();
        $default = $config->getCacheFile();

        $resolver = $this->createConfigurationResolver(
            [],
            $config,
        );

        self::assertSame($default, $resolver->getCacheFile());
    }

    public function testResolveCacheFileWithConfig(): void
    {
        $cacheFile = 'foo/bar.baz';

        $config = new Config();
        $config
            ->setUsingCache(false)
            ->setCacheFile($cacheFile)
        ;

        $resolver = $this->createConfigurationResolver(
            [],
            $config,
        );

        self::assertNull($resolver->getCacheFile());

        $cacheManager = $resolver->getCacheManager();

        self::assertInstanceOf(NullCacheManager::class, $cacheManager);

        self::assertFalse($resolver->getLinter()->isAsync());
    }

    public function testResolveCacheFileWithOption(): void
    {
        $cacheFile = 'bar.baz';

        $config = new Config();
        $config->setCacheFile($cacheFile);

        $resolver = $this->createConfigurationResolver(
            ['cache-file' => $cacheFile],
            $config,
        );

        self::assertSame($cacheFile, $resolver->getCacheFile());
    }

    public function testResolveCacheFileWithConfigAndOption(): void
    {
        $configCacheFile = 'foo/bar.baz';
        $optionCacheFile = 'bar.baz';

        $config = new Config();
        $config->setCacheFile($configCacheFile);

        $resolver = $this->createConfigurationResolver(
            ['cache-file' => $optionCacheFile],
            $config,
        );

        self::assertSame($optionCacheFile, $resolver->getCacheFile());
    }

    /**
     * @dataProvider provideResolveBooleanOptionCases
     */
    public function testResolveAllowRiskyWithConfigOption(bool $expected, bool $configValue, ?string $passed): void
    {
        $config = new Config();
        $config->setRiskyAllowed($configValue);

        $resolver = $this->createConfigurationResolver(
            ['allow-risky' => $passed],
            $config,
        );

        self::assertSame($expected, $resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndPositiveOption(): void
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            ['allow-risky' => 'yes'],
            $config,
        );

        self::assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNegativeOption(): void
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            ['allow-risky' => 'no'],
            $config,
        );

        self::assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndNoOption(): void
    {
        $config = new Config();
        $config->setRiskyAllowed(true);

        $resolver = $this->createConfigurationResolver(
            [],
            $config,
        );

        self::assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNoOption(): void
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            [],
            $config,
        );

        self::assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveRulesWithConfig(): void
    {
        $config = new Config();
        $config->setRules([
            'statement_indentation' => true,
            'strict_comparison' => false,
        ]);

        $resolver = $this->createConfigurationResolver(
            [],
            $config,
        );

        self::assertSameRules(
            [
                'statement_indentation' => true,
            ],
            $resolver->getRules(),
        );
    }

    public function testResolveRulesWithOption(): void
    {
        $resolver = $this->createConfigurationResolver(['rules' => 'statement_indentation,-strict_comparison']);

        self::assertSameRules(
            [
                'statement_indentation' => true,
            ],
            $resolver->getRules(),
        );
    }

    /**
     * @param list<string> $rules
     *
     * @dataProvider provideResolveRenamedRulesWithUnknownRulesCases
     */
    public function testResolveRenamedRulesWithUnknownRules(string $expectedMessage, array $rules): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($expectedMessage);

        $resolver = $this->createConfigurationResolver(['rules' => implode(',', $rules)]);
        $resolver->getRules();
    }

    /**
     * @return iterable<array{string, list<string>}>
     */
    public static function provideResolveRenamedRulesWithUnknownRulesCases(): iterable
    {
        yield 'with config' => [
            'The rules contain unknown fixers: "blank_line_before_return" is renamed (did you mean "blank_line_before_statement"? (note: use configuration "[\'statements\' => [\'return\']]")).
For more info about updating see: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/v3.0.0/UPGRADE-v3.md#renamed-ruless.',
            ['blank_line_before_return'],
        ];

        yield 'without config' => [
            'The rules contain unknown fixers: "final_static_access" is renamed (did you mean "self_static_accessor"?).
For more info about updating see: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/v3.0.0/UPGRADE-v3.md#renamed-ruless.',
            ['final_static_access'],
        ];

        yield [
            'The rules contain unknown fixers: "hash_to_slash_comment" is renamed (did you mean "single_line_comment_style"? (note: use configuration "[\'comment_types\' => [\'hash\']]")).
For more info about updating see: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/v3.0.0/UPGRADE-v3.md#renamed-ruless.',
            ['hash_to_slash_comment'],
        ];

        yield 'both renamed and unknown' => [
            'The rules contain unknown fixers: "final_static_access" is renamed (did you mean "self_static_accessor"?), "binary_operator_space" (did you mean "binary_operator_spaces"?).
For more info about updating see: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/v3.0.0/UPGRADE-v3.md#renamed-ruless.',
            ['final_static_access', 'binary_operator_space'],
        ];
    }

    public function testResolveRulesWithUnknownRules(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The rules contain unknown fixers: "bar", "binary_operator_space" (did you mean "binary_operator_spaces"?).');

        $resolver = $this->createConfigurationResolver(['rules' => 'statement_indentation,-bar,binary_operator_space']);
        $resolver->getRules();
    }

    public function testResolveRulesWithConfigAndOption(): void
    {
        $config = new Config();
        $config->setRules([
            'statement_indentation' => true,
            'strict_comparison' => false,
        ]);

        $resolver = $this->createConfigurationResolver(
            ['rules' => 'blank_line_before_statement'],
            $config,
        );

        self::assertSameRules(
            [
                'blank_line_before_statement' => true,
            ],
            $resolver->getRules(),
        );
    }

    public function testResolveCommandLineInputOverridesDefault(): void
    {
        $command = new FixCommand($this->createToolInfoDouble());
        $definition = $command->getDefinition();
        $arguments = $definition->getArguments();
        self::assertCount(1, $arguments, 'Expected one argument, possibly test needs updating.');
        self::assertArrayHasKey('path', $arguments);

        $options = $definition->getOptions();
        self::assertSame(
            ['path-mode', 'allow-risky', 'config', 'dry-run', 'rules', 'using-cache', 'allow-unsupported-php-version', 'cache-file', 'diff', 'format', 'stop-on-violation', 'show-progress', 'sequential'],
            array_keys($options),
            'Expected options mismatch, possibly test needs updating.',
        );

        $resolver = $this->createConfigurationResolver([
            'path-mode' => 'intersection',
            'allow-risky' => 'yes',
            'config' => null,
            'dry-run' => true,
            'rules' => 'php_unit_construct',
            'using-cache' => 'no',
            'diff' => true,
            'format' => 'json',
            'stop-on-violation' => true,
        ]);

        self::assertTrue($resolver->shouldStopOnViolation());
        self::assertTrue($resolver->getRiskyAllowed());
        self::assertTrue($resolver->isDryRun());
        self::assertSame(['php_unit_construct' => true], $resolver->getRules());
        self::assertFalse($resolver->getUsingCache());
        self::assertNull($resolver->getCacheFile());
        self::assertInstanceOf(UnifiedDiffer::class, $resolver->getDiffer());
        self::assertSame('json', $resolver->getReporter()->getFormat());
        self::assertSame('none', $resolver->getProgressType());
    }

    /**
     * @param class-string     $expected
     * @param null|bool|string $diffConfig
     *
     * @dataProvider provideResolveDifferCases
     */
    public function testResolveDiffer(string $expected, $diffConfig): void
    {
        $resolver = $this->createConfigurationResolver([
            'diff' => $diffConfig,
        ]);

        self::assertInstanceOf($expected, $resolver->getDiffer());
    }

    /**
     * @return iterable<int, array{string, null|bool}>
     */
    public static function provideResolveDifferCases(): iterable
    {
        yield [
            NullDiffer::class,
            false,
        ];

        yield [
            NullDiffer::class,
            null,
        ];

        yield [
            UnifiedDiffer::class,
            true,
        ];
    }

    public function testResolveConfigFileOverridesDefault(): void
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_8';

        $resolver = $this->createConfigurationResolver(['path' => [$dir.\DIRECTORY_SEPARATOR.'.php-cs-fixer.php']]);

        self::assertTrue($resolver->getRiskyAllowed());
        self::assertSame(['php_unit_construct' => true], $resolver->getRules());
        self::assertFalse($resolver->getUsingCache());
        self::assertNull($resolver->getCacheFile());
        self::assertSame('xml', $resolver->getReporter()->getFormat());
        self::assertSame('none', $resolver->getProgressType());
    }

    public function testDeprecationOfPassingOtherThanNoOrYes(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Expected "yes" or "no" for option "allow-risky", got "yes please".');

        $resolver = $this->createConfigurationResolver(['allow-risky' => 'yes please']);

        $resolver->getRiskyAllowed();
    }

    public function testWithEmptyRules(): void
    {
        $resolver = $this->createConfigurationResolver(['rules' => '']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^Empty rules value is not allowed\.$/');

        $resolver->getRules();
    }

    /**
     * @param array<string, mixed>|bool $ruleConfig
     *
     * @dataProvider provideDeprecatedFixerConfiguredCases
     *
     * @group legacy
     */
    public function testDeprecatedFixerConfigured($ruleConfig): void
    {
        $this->expectDeprecation('Rule "Vendor4/foo" is deprecated. Use "testA" and "testB" instead.');
        $fixer = $this->createDeprecatedFixerDouble();
        $config = new Config();
        $config->registerCustomFixers([$fixer]);
        $config->setRules([$fixer->getName() => $ruleConfig]);

        $resolver = $this->createConfigurationResolver([], $config);
        $resolver->getFixers();
    }

    /**
     * @return iterable<int, array{array<string, mixed>|bool}>
     */
    public static function provideDeprecatedFixerConfiguredCases(): iterable
    {
        yield [true];

        yield [['foo' => true]];

        yield [false];
    }

    public function testItCanRegisterCustomRuleSets(): void
    {
        $ruleSet = new ExampleRuleSet(__METHOD__);

        $config = new Config();
        $config->registerCustomRuleSets([$ruleSet]);
        $this
            ->createConfigurationResolver([], $config)
            ->getConfig() // IMPORTANT! Triggers custom rule sets registration
        ;

        self::assertContains($ruleSet->getName(), RuleSets::getSetDefinitionNames());
    }

    /**
     * @dataProvider provideDeprecatedRuleSetConfiguredCases
     *
     * @group legacy
     *
     * @param list<string> $successors
     */
    public function testDeprecatedRuleSetConfigured(string $ruleSet, array $successors): void
    {
        $this->expectDeprecation(\sprintf(
            'Rule set "%s" is deprecated. %s.',
            $ruleSet,
            [] === $successors
                ? 'No replacement available'
                : \sprintf('Use %s instead', Utils::naturalLanguageJoin($successors)),
        ));

        $config = new Config();
        $config->setRules([$ruleSet => true]);
        $config->setRiskyAllowed(true);

        $resolver = $this->createConfigurationResolver([], $config);
        $resolver->getFixers();
    }

    /**
     * @return iterable<int, array{0: string, 1: list<string>}>
     */
    public static function provideDeprecatedRuleSetConfiguredCases(): iterable
    {
        yield ['@PER', ['@PER-CS']];

        yield ['@PER:risky', ['@PER-CS:risky']];
    }

    /**
     * @dataProvider provideGetDirectoryCases
     *
     * @param ?non-empty-string $cacheFile
     * @param non-empty-string  $file
     * @param non-empty-string  $expectedPathRelativeToFile
     */
    public function testGetDirectory(?string $cacheFile, string $file, string $expectedPathRelativeToFile): void
    {
        if (null !== $cacheFile) {
            $cacheFile = $this->normalizePath($cacheFile);
        }

        $file = $this->normalizePath($file);
        $expectedPathRelativeToFile = $this->normalizePath($expectedPathRelativeToFile);

        $config = new Config();

        if (null === $cacheFile) {
            $config->setUsingCache(false);
        } else {
            $config->setCacheFile($cacheFile);
        }

        $resolver = new ConfigurationResolver($config, [], $this->normalizePath('/my/path'), $this->createToolInfoDouble());
        $directory = $resolver->getDirectory();

        self::assertSame($expectedPathRelativeToFile, $directory->getRelativePathTo($file));
    }

    /**
     * @return iterable<int, array{null|string, string, string}>
     */
    public static function provideGetDirectoryCases(): iterable
    {
        yield [null, '/my/path/my/file', 'my/file'];

        yield ['/my/path/.php-cs-fixer.cache', '/my/path/my/file', 'my/file'];

        yield ['/my/path2/dir/.php-cs-fixer.cache', '/my/path2/dir/dir2/file', '/my/path2/dir/dir2/file'];

        yield ['dir/.php-cs-fixer.cache', '/my/path/dir/dir3/file', 'dir/dir3/file'];

        yield ['var/.php-cs-fixer.cache', '/my/path/my/file', 'my/file'];
    }

    /**
     * @param class-string         $expectedFormat
     * @param array<string,string> $envs
     *
     * @dataProvider provideGetReporterCases
     *
     * @runInSeparateProcess
     */
    public function testGetReporter(string $expectedFormat, string $formatConfig, array $envs = []): void
    {
        foreach ($envs as $env => $val) {
            putenv("{$env}={$val}");
        }

        $resolver = $this->createConfigurationResolver([
            'format' => $formatConfig,
        ]);

        self::assertInstanceOf($expectedFormat, $resolver->getReporter());
    }

    /**
     * @return iterable<int, array{0: class-string, 1: string, 2?: array<string,string>}>
     */
    public static function provideGetReporterCases(): iterable
    {
        yield [
            CheckstyleReporter::class,
            'checkstyle',
        ];

        yield [
            TextReporter::class,
            'txt',
        ];

        yield [
            TextReporter::class,
            '@auto',
            ['GITHUB_ACTIONS' => '', 'GITLAB_CI' => ''],
        ];

        yield [
            GitHubReporter::class,
            '@auto',
            ['GITHUB_ACTIONS' => 'true'],
        ];

        yield [
            GitlabReporter::class,
            '@auto',
            ['GITHUB_ACTIONS' => '', 'GITLAB_CI' => 'true'],
        ];

        yield [
            JsonReporter::class,
            '@auto,json',
            ['GITHUB_ACTIONS' => ''],
        ];
    }

    /**
     * @param non-empty-string $path
     *
     * @return non-empty-string
     */
    private function normalizePath(string $path): string
    {
        return str_replace('/', \DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @param array<string, array<string, mixed>|bool> $expected
     * @param array<string, array<string, mixed>|bool> $actual
     */
    private static function assertSameRules(array $expected, array $actual): void
    {
        ksort($expected);
        ksort($actual);

        self::assertSame($expected, $actual);
    }

    private static function getFixtureDir(): string
    {
        return realpath(__DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.\DIRECTORY_SEPARATOR).'/';
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createConfigurationResolver(
        array $options,
        ?ConfigInterface $config = null,
        string $cwdPath = '',
        ?ToolInfoInterface $toolInfo = null
    ): ConfigurationResolver {
        return new ConfigurationResolver(
            $config ?? new Config(),
            $options,
            $cwdPath,
            $toolInfo ?? $this->createToolInfoDouble(),
        );
    }

    private function createDeprecatedFixerDouble(): DeprecatedFixerInterface
    {
        return new class extends AbstractFixer implements DeprecatedFixerInterface, ConfigurableFixerInterface {
            /** @use ConfigurableFixerTrait<array<string, mixed>, array<string, mixed>> */
            use ConfigurableFixerTrait;

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function getSuccessorsNames(): array
            {
                return ['testA', 'testB'];
            }

            public function getName(): string
            {
                return 'Vendor4/foo';
            }

            protected function applyFix(\SplFileInfo $file, Tokens $tokens): void {}

            protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
            {
                return new FixerConfigurationResolver([
                    (new FixerOptionBuilder('foo', 'Foo.'))->getOption(),
                ]);
            }
        };
    }

    private function createToolInfoDouble(): ToolInfoInterface
    {
        return new class implements ToolInfoInterface {
            public function getComposerInstallationDetails(): array
            {
                throw new \BadMethodCallException();
            }

            public function getComposerVersion(): string
            {
                throw new \BadMethodCallException();
            }

            public function getVersion(): string
            {
                throw new \BadMethodCallException();
            }

            public function isInstalledAsPhar(): bool
            {
                return true;
            }

            public function isInstalledByComposer(): bool
            {
                throw new \BadMethodCallException();
            }

            public function isRunInsideDocker(): bool
            {
                return false;
            }

            public function getPharDownloadUri(string $version): string
            {
                throw new \BadMethodCallException();
            }
        };
    }
}
