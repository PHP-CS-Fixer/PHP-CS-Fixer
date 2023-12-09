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
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Console\Output\Progress\ProgressOutputType;
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\ConfigurationResolver
 */
final class ConfigurationResolverTest extends TestCase
{
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

        self::assertSame('dots', $resolver->getProgressType());
    }

    public function testResolveProgressWithNegativeConfigAndNegativeOption(): void
    {
        $config = new Config();
        $config->setHideProgress(false);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ], $config);

        self::assertSame('none', $resolver->getProgressType());
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

    public static function provideProgressTypeCases(): iterable
    {
        foreach (ProgressOutputType::AVAILABLE as $outputType) {
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
        $this->expectExceptionMessage('The progress type "foo" is not defined, supported are "none" and "dots".');

        $resolver->getProgressType();
    }

    public function testResolveConfigFileDefault(): void
    {
        $resolver = $this->createConfigurationResolver([]);

        self::assertNull($resolver->getConfigFile());
        self::assertInstanceOf(\PhpCsFixer\ConfigInterface::class, $resolver->getConfig());
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
     */
    public function testResolveConfigFileChooseFile(string $expectedFile, string $expectedClass, string $path, ?string $cwdPath = null): void
    {
        $resolver = $this->createConfigurationResolver(
            ['path' => [$path]],
            null,
            $cwdPath ?? ''
        );

        self::assertSame($expectedFile, $resolver->getConfigFile());
        self::assertInstanceOf($expectedClass, $resolver->getConfig());
    }

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
            '#^The config file: ".+[\/\\\]Fixtures[\/\\\]ConfigurationResolverConfigFile[\/\\\]case_5[\/\\\]\.php-cs-fixer\.dist\.php" does not return a "PhpCsFixer\\\ConfigInterface" instance\. Got: "string"\.$#'
        );

        $dirBase = self::getFixtureDir();

        $resolver = $this->createConfigurationResolver(['path' => [$dirBase.'case_5']]);

        $resolver->getConfig();
    }

    public function testResolveConfigFileChooseFileWithInvalidFormat(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^The format "xls" is not defined, supported are "checkstyle", "gitlab", "json", "junit", "txt" and "xml"\.$/');

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
            $cwd
        );

        self::assertSame($expectedPaths, $resolver->getPath());
    }

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
     * @param array<string> $paths
     *
     * @dataProvider provideRejectInvalidPathCases
     */
    public function testRejectInvalidPath(array $paths, string $expectedMessage): void
    {
        $resolver = $this->createConfigurationResolver(
            ['path' => $paths],
            null,
            \dirname(__DIR__)
        );

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($expectedMessage);

        $resolver->getPath();
    }

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
        $config->getFinder()
            ->in(__DIR__)
            ->notPath(basename(__FILE__))
        ;

        $resolver = $this->createConfigurationResolver(
            ['path' => [__FILE__]],
            $config
        );

        self::assertCount(1, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedDirectlyIntersectionPathMode(): void
    {
        $config = new Config();
        $config->getFinder()
            ->in(__DIR__)
            ->notPath(basename(__FILE__))
        ;

        $resolver = $this->createConfigurationResolver([
            'path' => [__FILE__],
            'path-mode' => 'intersection',
        ], $config);

        self::assertCount(0, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedByDirOverridePathMode(): void
    {
        $dir = \dirname(__DIR__);
        $config = new Config();
        $config->getFinder()
            ->in($dir)
            ->exclude(basename(__DIR__))
        ;

        $resolver = $this->createConfigurationResolver(
            ['path' => [__FILE__]],
            $config
        );

        self::assertCount(1, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedByDirIntersectionPathMode(): void
    {
        $dir = \dirname(__DIR__);
        $config = new Config();
        $config->getFinder()
            ->in($dir)
            ->exclude(basename(__DIR__))
        ;

        $resolver = $this->createConfigurationResolver([
            'path-mode' => 'intersection',
            'path' => [__FILE__],
        ], $config);

        self::assertCount(0, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsNotExcluded(): void
    {
        $dir = __DIR__;
        $config = new Config();
        $config->getFinder()
            ->in($dir)
            ->notPath('foo-'.basename(__FILE__))
        ;

        $resolver = $this->createConfigurationResolver(
            ['path' => [__FILE__]],
            $config
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
            iterator_to_array($resolver->getFinder(), false)
        );

        sort($expected);
        sort($intersectionItems);

        self::assertSame($expected, $intersectionItems);
    }

    public static function provideResolveIntersectionOfPathsCases(): iterable
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverPathsIntersection';
        $cb = static fn (array $items): array => array_map(
            static fn (string $item): string => realpath($dir.'/'.$item),
            $items
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

    public static function provideConfigFinderIsOverriddenCases(): iterable
    {
        $root = __DIR__.'/../..';

        yield [
            [
                'config' => $root.'/.php-cs-fixer.dist.php',
            ],
            false,
        ];

        yield [
            [
                'config' => $root.'/.php-cs-fixer.dist.php',
                'path' => [$root.'/src'],
            ],
            true,
        ];

        yield [
            [],
            false,
        ];

        yield [
            [
                'path' => [$root.'/src'],
            ],
            false,
        ];

        yield [
            [
                'config' => $root.'/.php-cs-fixer.dist.php',
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
            $config
        );

        self::assertSame($expected, $resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithPositiveConfigAndNoOption(): void
    {
        $config = new Config();
        $config->setUsingCache(true);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        self::assertTrue($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndNoOption(): void
    {
        $config = new Config();
        $config->setUsingCache(false);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        self::assertFalse($resolver->getUsingCache());
    }

    public function testResolveCacheFileWithoutConfigAndOption(): void
    {
        $config = new Config();
        $default = $config->getCacheFile();

        $resolver = $this->createConfigurationResolver(
            [],
            $config
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
            $config
        );

        self::assertNull($resolver->getCacheFile());

        $cacheManager = $resolver->getCacheManager();

        self::assertInstanceOf(NullCacheManager::class, $cacheManager);

        $linter = $resolver->getLinter();

        self::assertInstanceOf(LinterInterface::class, $linter);
    }

    public function testResolveCacheFileWithOption(): void
    {
        $cacheFile = 'bar.baz';

        $config = new Config();
        $config->setCacheFile($cacheFile);

        $resolver = $this->createConfigurationResolver(
            ['cache-file' => $cacheFile],
            $config
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
            $config
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
            $config
        );

        self::assertSame($expected, $resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndPositiveOption(): void
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            ['allow-risky' => 'yes'],
            $config
        );

        self::assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNegativeOption(): void
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            ['allow-risky' => 'no'],
            $config
        );

        self::assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndNoOption(): void
    {
        $config = new Config();
        $config->setRiskyAllowed(true);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        self::assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNoOption(): void
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
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
            $config
        );

        self::assertSameRules(
            [
                'statement_indentation' => true,
            ],
            $resolver->getRules()
        );
    }

    public function testResolveRulesWithOption(): void
    {
        $resolver = $this->createConfigurationResolver(['rules' => 'statement_indentation,-strict_comparison']);

        self::assertSameRules(
            [
                'statement_indentation' => true,
            ],
            $resolver->getRules()
        );
    }

    /**
     * @param string[] $rules
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
            $config
        );

        self::assertSameRules(
            [
                'blank_line_before_statement' => true,
            ],
            $resolver->getRules()
        );
    }

    public function testResolveCommandLineInputOverridesDefault(): void
    {
        $command = new FixCommand(new ToolInfo());
        $definition = $command->getDefinition();
        $arguments = $definition->getArguments();
        self::assertCount(1, $arguments, 'Expected one argument, possibly test needs updating.');
        self::assertArrayHasKey('path', $arguments);

        $options = $definition->getOptions();
        self::assertSame(
            ['path-mode', 'allow-risky', 'config', 'dry-run', 'rules', 'using-cache', 'cache-file', 'diff', 'format', 'stop-on-violation', 'show-progress'],
            array_keys($options),
            'Expected options mismatch, possibly test needs updating.'
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
        self::assertInstanceOf(\PhpCsFixer\Differ\UnifiedDiffer::class, $resolver->getDiffer());
        self::assertSame('json', $resolver->getReporter()->getFormat());
    }

    /**
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

    public static function provideResolveDifferCases(): iterable
    {
        yield [
            \PhpCsFixer\Differ\NullDiffer::class,
            false,
        ];

        yield [
            \PhpCsFixer\Differ\NullDiffer::class,
            null,
        ];

        yield [
            \PhpCsFixer\Differ\UnifiedDiffer::class,
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
    }

    public function testDeprecationOfPassingOtherThanNoOrYes(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Expected "yes" or "no" for option "allow-risky", got "yes please".');

        $resolver = $this->createConfigurationResolver(['allow-risky' => 'yes please']);

        $resolver->getRiskyAllowed();
    }

    public static function provideResolveBooleanOptionCases(): iterable
    {
        yield [true, true, 'yes'];

        yield [true, false, 'yes'];

        yield [false, true, 'no'];

        yield [false, false, 'no'];

        yield [true, true, null];

        yield [false, false, null];
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

    public static function provideDeprecatedFixerConfiguredCases(): iterable
    {
        yield [true];

        yield [['foo' => true]];

        yield [false];
    }

    public static function provideGetDirectoryCases(): iterable
    {
        yield [null, '/my/path/my/file', 'my/file'];

        yield ['/my/path/.php-cs-fixer.cache', '/my/path/my/file', 'my/file'];

        yield ['/my/path2/dir/.php-cs-fixer.cache', '/my/path2/dir/dir2/file', 'dir2/file'];

        yield ['dir/.php-cs-fixer.cache', '/my/path/dir/dir3/file', 'dir3/file'];
    }

    /**
     * @dataProvider provideGetDirectoryCases
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

        $resolver = new ConfigurationResolver($config, [], $this->normalizePath('/my/path'), new TestToolInfo());
        $directory = $resolver->getDirectory();

        self::assertSame($expectedPathRelativeToFile, $directory->getRelativePathTo($file));
    }

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
    private function createConfigurationResolver(array $options, Config $config = null, string $cwdPath = ''): ConfigurationResolver
    {
        if (null === $config) {
            $config = new Config();
        }

        return new ConfigurationResolver(
            $config,
            $options,
            $cwdPath,
            new TestToolInfo()
        );
    }

    private function createDeprecatedFixerDouble(): DeprecatedFixerInterface
    {
        return new class() extends AbstractFixer implements DeprecatedFixerInterface, ConfigurableFixerInterface {
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
}
