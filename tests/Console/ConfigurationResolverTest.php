<?php

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

use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Config;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Finder;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Tests\Fixtures\DeprecatedFixer;
use PhpCsFixer\Tests\Fixtures\FakeDiffer;
use PhpCsFixer\Tests\TestCase;
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
    public function testSetOptionWithUndefinedOption()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^Unknown option name: "foo"\.$/');

        $this->createConfigurationResolver(['foo' => 'bar']);
    }

    public function testResolveProgressWithPositiveConfigAndPositiveOption()
    {
        $config = new Config();
        $config->setHideProgress(true);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
        ], $config);

        static::assertSame('none', $resolver->getProgress());
    }

    public function testResolveProgressWithPositiveConfigAndNegativeOption()
    {
        $config = new Config();
        $config->setHideProgress(true);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ], $config);

        static::assertSame('none', $resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndPositiveOption()
    {
        $config = new Config();
        $config->setHideProgress(false);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
        ], $config);

        static::assertSame('run-in', $resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndNegativeOption()
    {
        $config = new Config();
        $config->setHideProgress(false);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ], $config);

        static::assertSame('none', $resolver->getProgress());
    }

    /**
     * @param string $progressType
     *
     * @dataProvider provideProgressTypeCases
     */
    public function testResolveProgressWithPositiveConfigAndExplicitProgress($progressType)
    {
        $config = new Config();
        $config->setHideProgress(true);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            'show-progress' => $progressType,
        ], $config);

        static::assertSame($progressType, $resolver->getProgress());
    }

    /**
     * @param string $progressType
     *
     * @dataProvider provideProgressTypeLegacyCases
     *
     * @group legacy
     */
    public function testResolveProgressWithPositiveConfigAndExplicitProgressLegacy($progressType)
    {
        $this->expectDeprecation('Passing `estimating`, `estimating-max` or `run-in` is deprecated and will not be supported in 3.0, use `none` or `dots` instead.');
        $this->testResolveProgressWithPositiveConfigAndExplicitProgress($progressType);
    }

    /**
     * @param string $progressType
     *
     * @dataProvider provideProgressTypeCases
     */
    public function testResolveProgressWithNegativeConfigAndExplicitProgress($progressType)
    {
        $config = new Config();
        $config->setHideProgress(false);

        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            'show-progress' => $progressType,
        ], $config);

        static::assertSame($progressType, $resolver->getProgress());
    }

    /**
     * @param string $progressType
     *
     * @dataProvider provideProgressTypeLegacyCases
     *
     * @group legacy
     */
    public function testResolveProgressWithNegativeConfigAndExplicitProgressLegacy($progressType)
    {
        $this->expectDeprecation('Passing `estimating`, `estimating-max` or `run-in` is deprecated and will not be supported in 3.0, use `none` or `dots` instead.');
        $this->testResolveProgressWithNegativeConfigAndExplicitProgress($progressType);
    }

    public function provideProgressTypeLegacyCases()
    {
        return [
            ['run-in'],
            ['estimating'],
            ['estimating-max'],
        ];
    }

    public function provideProgressTypeCases()
    {
        return [
            ['none'],
            ['dots'],
        ];
    }

    public function testResolveProgressWithInvalidExplicitProgress()
    {
        $resolver = $this->createConfigurationResolver([
            'format' => 'txt',
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            'show-progress' => 'foo',
        ]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The progress type "foo" is not defined, supported are "none", "run-in", "estimating", "estimating-max", "dots".');

        $resolver->getProgress();
    }

    public function testResolveConfigFileDefault()
    {
        $resolver = $this->createConfigurationResolver([]);

        static::assertNull($resolver->getConfigFile());
        static::assertInstanceOf(\PhpCsFixer\ConfigInterface::class, $resolver->getConfig());
    }

    public function testResolveConfigFileByPathOfFile()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_1';

        $resolver = $this->createConfigurationResolver(['path' => [$dir.\DIRECTORY_SEPARATOR.'foo.php']]);

        static::assertSame($dir.\DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php', $resolver->getConfigFile());
        static::assertInstanceOf(\Test1Config::class, $resolver->getConfig()); // @phpstan-ignore-line to avoid `Class Test1Config not found.`
    }

    public function testResolveConfigFileSpecified()
    {
        $file = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_4/my.php-cs-fixer.php';

        $resolver = $this->createConfigurationResolver(['config' => $file]);

        static::assertSame($file, $resolver->getConfigFile());
        static::assertInstanceOf(\Test4Config::class, $resolver->getConfig()); // @phpstan-ignore-line to avoid `Class Test4Config not found.`
    }

    /**
     * @param string      $expectedFile
     * @param string      $expectedClass
     * @param string      $path
     * @param null|string $cwdPath
     *
     * @dataProvider provideResolveConfigFileDefaultCases
     */
    public function testResolveConfigFileChooseFile($expectedFile, $expectedClass, $path, $cwdPath = null)
    {
        $resolver = $this->createConfigurationResolver(
            ['path' => [$path]],
            null,
            $cwdPath ?: ''
        );

        static::assertSame($expectedFile, $resolver->getConfigFile());
        static::assertInstanceOf($expectedClass, $resolver->getConfig());
    }

    public function provideResolveConfigFileDefaultCases()
    {
        $dirBase = $this->getFixtureDir();

        return [
            [
                $dirBase.'case_1'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php',
                'Test1Config',
                $dirBase.'case_1',
            ],
            [
                $dirBase.'case_2'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.php',
                'Test2Config',
                $dirBase.'case_2',
            ],
            [
                $dirBase.'case_3'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.php',
                'Test3Config',
                $dirBase.'case_3',
            ],
            [
                $dirBase.'case_6'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php',
                'Test6Config',
                $dirBase.'case_6'.\DIRECTORY_SEPARATOR.'subdir',
                $dirBase.'case_6',
            ],
            [
                $dirBase.'case_6'.\DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php',
                'Test6Config',
                $dirBase.'case_6'.\DIRECTORY_SEPARATOR.'subdir/empty_file.php',
                $dirBase.'case_6',
            ],
        ];
    }

    public function testResolveConfigFileChooseFileWithInvalidFile()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '#^The config file: ".+[\/\\\]Fixtures[\/\\\]ConfigurationResolverConfigFile[\/\\\]case_5[\/\\\]\.php-cs-fixer\.dist\.php" does not return a "PhpCsFixer\\\ConfigInterface" instance\. Got: "string"\.$#'
        );

        $dirBase = $this->getFixtureDir();

        $resolver = $this->createConfigurationResolver(['path' => [$dirBase.'case_5']]);

        $resolver->getConfig();
    }

    public function testResolveConfigFileChooseFileWithInvalidFormat()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^The format "xls" is not defined, supported are "checkstyle", "gitlab", "json", "junit", "txt", "xml"\.$/');

        $dirBase = $this->getFixtureDir();

        $resolver = $this->createConfigurationResolver(['path' => [$dirBase.'case_7']]);

        $resolver->getReporter();
    }

    public function testResolveConfigFileChooseFileWithPathArrayWithoutConfig()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^For multiple paths config parameter is required\.$/');

        $dirBase = $this->getFixtureDir();

        $resolver = $this->createConfigurationResolver(['path' => [$dirBase.'case_1/.php-cs-fixer.dist.php', $dirBase.'case_1/foo.php']]);

        $resolver->getConfig();
    }

    public function testResolveConfigFileChooseFileWithPathArrayAndConfig()
    {
        $dirBase = $this->getFixtureDir();

        $resolver = $this->createConfigurationResolver([
            'config' => $dirBase.'case_1/.php-cs-fixer.dist.php',
            'path' => [$dirBase.'case_1/.php-cs-fixer.dist.php', $dirBase.'case_1/foo.php'],
        ]);

        static::assertInstanceOf(\PhpCsFixer\Console\ConfigurationResolver::class, $resolver);
    }

    /**
     * @param array<int, string> $paths
     * @param string             $cwd
     * @param array<int, string> $expectedPaths
     *
     * @dataProvider providePathCases
     */
    public function testResolvePath(array $paths, $cwd, array $expectedPaths)
    {
        $resolver = $this->createConfigurationResolver(
            ['path' => $paths],
            null,
            $cwd
        );

        static::assertSame($expectedPaths, $resolver->getPath());
    }

    public function providePathCases()
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
     * @param string        $expectedMessage
     *
     * @dataProvider provideEmptyPathCases
     */
    public function testRejectInvalidPath(array $paths, $expectedMessage)
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

    public function provideEmptyPathCases()
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

    public function testResolvePathWithFileThatIsExcludedDirectlyOverridePathMode()
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

        static::assertCount(1, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedDirectlyIntersectionPathMode()
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

        static::assertCount(0, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedByDirOverridePathMode()
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

        static::assertCount(1, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedByDirIntersectionPathMode()
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

        static::assertCount(0, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsNotExcluded()
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

        static::assertCount(1, $resolver->getFinder());
    }

    /**
     * @param array|\Exception $expected
     * @param null|Finder      $configFinder
     * @param string           $pathMode
     * @param null|string      $configOption
     *
     * @dataProvider provideResolveIntersectionOfPathsCases
     */
    public function testResolveIntersectionOfPaths($expected, $configFinder, array $path, $pathMode, $configOption = null)
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
            static function (\SplFileInfo $file) {
                return $file->getRealPath();
            },
            iterator_to_array($resolver->getFinder(), false)
        );

        sort($expected);
        sort($intersectionItems);

        static::assertSame($expected, $intersectionItems);
    }

    public function provideResolveIntersectionOfPathsCases()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverPathsIntersection';
        $cb = static function (array $items) use ($dir) {
            return array_map(
                static function ($item) use ($dir) {
                    return realpath($dir.'/'.$item);
                },
                $items
            );
        };

        return [
            'no path at all' => [
                new \LogicException(),
                Finder::create(),
                [],
                'override',
            ],
            'configured only by finder' => [
                // don't override if the argument is empty
                $cb(['a1.php', 'a2.php', 'b/b1.php', 'b/b2.php', 'b_b/b_b1.php', 'c/c1.php', 'c/d/cd1.php', 'd/d1.php', 'd/d2.php', 'd/e/de1.php', 'd/f/df1.php']),
                Finder::create()
                    ->in($dir),
                [],
                'override',
            ],
            'configured only by argument' => [
                $cb(['a1.php', 'a2.php', 'b/b1.php', 'b/b2.php', 'b_b/b_b1.php', 'c/c1.php', 'c/d/cd1.php', 'd/d1.php', 'd/d2.php', 'd/e/de1.php', 'd/f/df1.php']),
                Finder::create(),
                [$dir],
                'override',
            ],
            'configured by finder, intersected with empty argument' => [
                [],
                Finder::create()
                    ->in($dir),
                [],
                'intersection',
            ],
            'configured by finder, intersected with dir' => [
                $cb(['c/c1.php', 'c/d/cd1.php']),
                Finder::create()
                    ->in($dir),
                [$dir.'/c'],
                'intersection',
            ],
            'configured by finder, intersected with file' => [
                $cb(['c/c1.php']),
                Finder::create()
                    ->in($dir),
                [$dir.'/c/c1.php'],
                'intersection',
            ],
            'finder points to one dir while argument to another, not connected' => [
                [],
                Finder::create()
                    ->in($dir.'/b'),
                [$dir.'/c'],
                'intersection',
            ],
            'finder with excluded dir, intersected with excluded file' => [
                [],
                Finder::create()
                    ->in($dir)
                    ->exclude('c'),
                [$dir.'/c/d/cd1.php'],
                'intersection',
            ],
            'finder with excluded dir, intersected with dir containing excluded one' => [
                $cb(['c/c1.php']),
                Finder::create()
                    ->in($dir)
                    ->exclude('c/d'),
                [$dir.'/c'],
                'intersection',
            ],
            'finder with excluded file, intersected with dir containing excluded one' => [
                $cb(['c/d/cd1.php']),
                Finder::create()
                    ->in($dir)
                    ->notPath('c/c1.php'),
                [$dir.'/c'],
                'intersection',
            ],
            'configured by finder, intersected with non-existing path' => [
                new \LogicException(),
                Finder::create()
                    ->in($dir),
                ['non_existing_dir'],
                'intersection',
            ],
            'configured by config file, overridden by multiple files' => [
                $cb(['d/d1.php', 'd/d2.php']),
                null,
                [$dir.'/d/d1.php', $dir.'/d/d2.php'],
                'override',
                $dir.'/d/.php-cs-fixer.php',
            ],
            'configured by config file, intersected with multiple files' => [
                $cb(['d/d1.php', 'd/d2.php']),
                null,
                [$dir.'/d/d1.php', $dir.'/d/d2.php'],
                'intersection',
                $dir.'/d/.php-cs-fixer.php',
            ],
            'configured by config file, overridden by non-existing dir' => [
                new \LogicException(),
                null,
                [$dir.'/d/fff'],
                'override',
                $dir.'/d/.php-cs-fixer.php',
            ],
            'configured by config file, intersected with non-existing dir' => [
                new \LogicException(),
                null,
                [$dir.'/d/fff'],
                'intersection',
                $dir.'/d/.php-cs-fixer.php',
            ],
            'configured by config file, overridden by non-existing file' => [
                new \LogicException(),
                null,
                [$dir.'/d/fff.php'],
                'override',
                $dir.'/d/.php-cs-fixer.php',
            ],
            'configured by config file, intersected with non-existing file' => [
                new \LogicException(),
                null,
                [$dir.'/d/fff.php'],
                'intersection',
                $dir.'/d/.php-cs-fixer.php',
            ],
            'configured by config file, overridden by multiple files and dirs' => [
                $cb(['d/d1.php', 'd/e/de1.php', 'd/f/df1.php']),
                null,
                [$dir.'/d/d1.php', $dir.'/d/e', $dir.'/d/f/'],
                'override',
                $dir.'/d/.php-cs-fixer.php',
            ],
            'configured by config file, intersected with multiple files and dirs' => [
                $cb(['d/d1.php', 'd/e/de1.php', 'd/f/df1.php']),
                null,
                [$dir.'/d/d1.php', $dir.'/d/e', $dir.'/d/f/'],
                'intersection',
                $dir.'/d/.php-cs-fixer.php',
            ],
        ];
    }

    /**
     * @param bool $expectedResult
     *
     * @dataProvider provideConfigFinderIsOverriddenCases
     */
    public function testConfigFinderIsOverridden(array $options, $expectedResult)
    {
        $resolver = $this->createConfigurationResolver($options);

        static::assertSame($expectedResult, $resolver->configFinderIsOverridden());

        $resolver = $this->createConfigurationResolver($options);
        $resolver->getFinder();

        static::assertSame($expectedResult, $resolver->configFinderIsOverridden());
    }

    public function provideConfigFinderIsOverriddenCases()
    {
        $root = __DIR__.'/../..';

        return [
            [
                [
                    'config' => $root.'/.php-cs-fixer.dist.php',
                ],
                false,
            ],
            [
                [
                    'config' => $root.'/.php-cs-fixer.dist.php',
                    'path' => [$root.'/src'],
                ],
                true,
            ],
            [
                [],
                false,
            ],
            [
                [
                    'path' => [$root.'/src'],
                ],
                false,
            ],
            [
                [
                    'config' => $root.'/.php-cs-fixer.dist.php',
                    'path' => [$root.'/src'],
                    'path-mode' => ConfigurationResolver::PATH_MODE_INTERSECTION,
                ],
                false,
            ],
            // scenario when loaded config is not setting custom finder
            [
                [
                    'config' => $root.'/tests/Fixtures/ConfigurationResolverConfigFile/case_3/.php-cs-fixer.dist.php',
                    'path' => [$root.'/src'],
                ],
                false,
            ],
            // scenario when loaded config contains not fully defined finder
            [
                [
                    'config' => $root.'/tests/Fixtures/ConfigurationResolverConfigFile/case_9/.php-cs-fixer.php',
                    'path' => [$root.'/src'],
                ],
                false,
            ],
        ];
    }

    public function testResolveIsDryRunViaStdIn()
    {
        $resolver = $this->createConfigurationResolver([
            'dry-run' => false,
            'path' => ['-'],
        ]);

        static::assertTrue($resolver->isDryRun());
    }

    public function testResolveIsDryRunViaNegativeOption()
    {
        $resolver = $this->createConfigurationResolver(['dry-run' => false]);

        static::assertFalse($resolver->isDryRun());
    }

    public function testResolveIsDryRunViaPositiveOption()
    {
        $resolver = $this->createConfigurationResolver(['dry-run' => true]);

        static::assertTrue($resolver->isDryRun());
    }

    /**
     * @param bool             $expected
     * @param bool             $configValue
     * @param null|bool|string $passed
     *
     * @dataProvider provideResolveBooleanOptionCases
     */
    public function testResolveUsingCacheWithConfigOption($expected, $configValue, $passed)
    {
        $config = new Config();
        $config->setUsingCache($configValue);

        $resolver = $this->createConfigurationResolver(
            ['using-cache' => $passed],
            $config
        );

        static::assertSame($expected, $resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithPositiveConfigAndNoOption()
    {
        $config = new Config();
        $config->setUsingCache(true);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        static::assertTrue($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndNoOption()
    {
        $config = new Config();
        $config->setUsingCache(false);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        static::assertFalse($resolver->getUsingCache());
    }

    public function testResolveCacheFileWithoutConfigAndOption()
    {
        $config = new Config();
        $default = $config->getCacheFile();

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        static::assertSame($default, $resolver->getCacheFile());
    }

    public function testResolveCacheFileWithConfig()
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

        static::assertNull($resolver->getCacheFile());

        $cacheManager = $resolver->getCacheManager();

        static::assertInstanceOf(NullCacheManager::class, $cacheManager);

        $linter = $resolver->getLinter();

        static::assertInstanceOf(LinterInterface::class, $linter);
    }

    public function testResolveCacheFileWithOption()
    {
        $cacheFile = 'bar.baz';

        $config = new Config();
        $config->setCacheFile($cacheFile);

        $resolver = $this->createConfigurationResolver(
            ['cache-file' => $cacheFile],
            $config
        );

        static::assertSame($cacheFile, $resolver->getCacheFile());
    }

    public function testResolveCacheFileWithConfigAndOption()
    {
        $configCacheFile = 'foo/bar.baz';
        $optionCacheFile = 'bar.baz';

        $config = new Config();
        $config->setCacheFile($configCacheFile);

        $resolver = $this->createConfigurationResolver(
            ['cache-file' => $optionCacheFile],
            $config
        );

        static::assertSame($optionCacheFile, $resolver->getCacheFile());
    }

    /**
     * @param bool             $expected
     * @param bool             $configValue
     * @param null|bool|string $passed
     *
     * @dataProvider provideResolveBooleanOptionCases
     */
    public function testResolveAllowRiskyWithConfigOption($expected, $configValue, $passed)
    {
        $config = new Config();
        $config->setRiskyAllowed($configValue);

        $resolver = $this->createConfigurationResolver(
            ['allow-risky' => $passed],
            $config
        );

        static::assertSame($expected, $resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndPositiveOption()
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            ['allow-risky' => 'yes'],
            $config
        );

        static::assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNegativeOption()
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            ['allow-risky' => 'no'],
            $config
        );

        static::assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndNoOption()
    {
        $config = new Config();
        $config->setRiskyAllowed(true);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        static::assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNoOption()
    {
        $config = new Config();
        $config->setRiskyAllowed(false);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        static::assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveRulesWithConfig()
    {
        $config = new Config();
        $config->setRules([
            'braces' => true,
            'strict_comparison' => false,
        ]);

        $resolver = $this->createConfigurationResolver(
            [],
            $config
        );

        static::assertSameRules(
            [
                'braces' => true,
            ],
            $resolver->getRules()
        );
    }

    public function testResolveRulesWithOption()
    {
        $resolver = $this->createConfigurationResolver(['rules' => 'braces,-strict_comparison']);

        static::assertSameRules(
            [
                'braces' => true,
            ],
            $resolver->getRules()
        );
    }

    public function testResolveRulesWithUnknownRules()
    {
        $this->expectException(
            \PhpCsFixer\ConfigurationException\InvalidConfigurationException::class
        );
        $this->expectExceptionMessage(
            'The rules contain unknown fixers: "bar", "binary_operator_space" (did you mean "binary_operator_spaces"?).'
        );

        $resolver = $this->createConfigurationResolver(['rules' => 'braces,-bar,binary_operator_space']);

        $resolver->getRules();
    }

    public function testResolveRulesWithConfigAndOption()
    {
        $config = new Config();
        $config->setRules([
            'braces' => true,
            'strict_comparison' => false,
        ]);

        $resolver = $this->createConfigurationResolver(
            ['rules' => 'blank_line_before_statement'],
            $config
        );

        static::assertSameRules(
            [
                'blank_line_before_statement' => true,
            ],
            $resolver->getRules()
        );
    }

    public function testResolveCommandLineInputOverridesDefault()
    {
        $command = new FixCommand(new ToolInfo());
        $definition = $command->getDefinition();
        $arguments = $definition->getArguments();
        static::assertCount(1, $arguments, 'Expected one argument, possibly test needs updating.');
        static::assertArrayHasKey('path', $arguments);

        $options = $definition->getOptions();
        static::assertSame(
            ['path-mode', 'allow-risky', 'config', 'dry-run', 'rules', 'using-cache', 'cache-file', 'diff', 'diff-format', 'format', 'stop-on-violation', 'show-progress'],
            array_keys($options),
            'Expected options mismatch, possibly test needs updating.'
        );

        $resolver = $this->createConfigurationResolver([
            'path-mode' => 'intersection',
            'allow-risky' => 'yes',
            'config' => null,
            'dry-run' => true,
            'rules' => 'php_unit_construct',
            'using-cache' => false,
            'diff' => true,
            'diff-format' => 'udiff',
            'format' => 'json',
            'stop-on-violation' => true,
        ]);

        static::assertTrue($resolver->shouldStopOnViolation());
        static::assertTrue($resolver->getRiskyAllowed());
        static::assertTrue($resolver->isDryRun());
        static::assertSame(['php_unit_construct' => true], $resolver->getRules());
        static::assertFalse($resolver->getUsingCache());
        static::assertNull($resolver->getCacheFile());
        static::assertInstanceOf(\PhpCsFixer\Differ\UnifiedDiffer::class, $resolver->getDiffer());
        static::assertSame('json', $resolver->getReporter()->getFormat());
    }

    /**
     * @param string           $expected
     * @param null|bool|string $diffConfig
     * @param null|string      $differConfig
     *
     * @dataProvider provideDifferCases
     */
    public function testResolveDiffer($expected, $diffConfig, $differConfig = null)
    {
        $resolver = $this->createConfigurationResolver([
            'diff' => $diffConfig,
            'diff-format' => $differConfig,
        ]);

        static::assertInstanceOf($expected, $resolver->getDiffer());
    }

    public function testCustomDiffer()
    {
        $resolver = $this->createConfigurationResolver([
            'diff-format' => FakeDiffer::class,
        ]);

        static::assertInstanceOf(FakeDiffer::class, $resolver->getDiffer());
    }

    public function testCustomDifferMustBeAString()
    {
        $resolver = $this->createConfigurationResolver([
            'diff-format' => new FakeDiffer(),
        ]);

        $this->expectExceptionMessage('"diff-format" must be a string, "object" given');

        $resolver->getDiffer();
    }

    public function provideDifferCases()
    {
        return [
            [
                \PhpCsFixer\Differ\NullDiffer::class,
                false,
            ],
            [
                \PhpCsFixer\Differ\NullDiffer::class,
                null,
            ],
            [
                \PhpCsFixer\Differ\SebastianBergmannDiffer::class,
                true,
            ],
            [
                \PhpCsFixer\Differ\SebastianBergmannDiffer::class,
                'sbd',
            ],
            [
                \PhpCsFixer\Differ\SebastianBergmannDiffer::class,
                true,
                'sbd',
            ],
            [
                \PhpCsFixer\Differ\SebastianBergmannDiffer::class,
                false,
                'sbd',
            ],
            [
                \PhpCsFixer\Differ\SebastianBergmannDiffer::class,
                null,
                'sbd',
            ],
            [
                \PhpCsFixer\Differ\UnifiedDiffer::class,
                true,
                'udiff',
            ],
            [
                \PhpCsFixer\Differ\UnifiedDiffer::class,
                false,
                'udiff',
            ],
            [
                \PhpCsFixer\Differ\UnifiedDiffer::class,
                null,
                'udiff',
            ],
        ];
    }

    public function testResolveUnknownDiffer()
    {
        $resolver = $this->createConfigurationResolver([
            'diff-format' => 'XXX',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^"diff\-format" must be any of "null", "sbd", "udiff", got "XXX"\.$#');

        $resolver->getDiffer();
    }

    public function testResolveConfigFileOverridesDefault()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_8';

        $resolver = $this->createConfigurationResolver(['path' => [$dir.\DIRECTORY_SEPARATOR.'.php-cs-fixer.php']]);

        static::assertTrue($resolver->getRiskyAllowed());
        static::assertSame(['php_unit_construct' => true], $resolver->getRules());
        static::assertFalse($resolver->getUsingCache());
        static::assertNull($resolver->getCacheFile());
        static::assertSame('xml', $resolver->getReporter()->getFormat());
    }

    /**
     * @group legacy
     */
    public function testDeprecationOfPassingOtherThanNoOrYes()
    {
        $this->expectDeprecation('Expected "yes" or "no" for option "allow-risky", other values are deprecated and support will be removed in 3.0. Got "yes please", this implicitly set the option to "false".');
        $resolver = $this->createConfigurationResolver(['allow-risky' => 'yes please']);

        static::assertFalse($resolver->getRiskyAllowed());
    }

    public function provideResolveBooleanOptionCases()
    {
        return [
            [true, true, 'yes'],
            [true, true, true],
            [true, false, 'yes'],
            [true, false, true],
            [false, true, 'no'],
            [false, true, false],
            [false, false, 'no'],
            [false, false, false],
            [true, true, null],
            [false, false, null],
        ];
    }

    public function testWithEmptyRules()
    {
        $resolver = $this->createConfigurationResolver(['rules' => '']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/^Empty rules value is not allowed\.$/');

        $resolver->getRules();
    }

    /**
     * @param array|bool $ruleConfig
     *
     * @dataProvider provideDeprecatedFixerConfiguredCases
     *
     * @group legacy
     */
    public function testDeprecatedFixerConfigured($ruleConfig)
    {
        $this->expectDeprecation('Rule "Vendor4/foo" is deprecated. Use "testA" and "testB" instead.');
        $fixer = new DeprecatedFixer();
        $config = new Config();
        $config->registerCustomFixers([$fixer]);
        $config->setRules([$fixer->getName() => $ruleConfig]);

        $resolver = $this->createConfigurationResolver([], $config);
        $resolver->getFixers();
    }

    public function provideDeprecatedFixerConfiguredCases()
    {
        return [
            [true],
            [['foo' => true]],
            [false],
        ];
    }

    public function provideGetDirectoryCases()
    {
        return [
            [null, '/my/path/my/file', 'my/file'],
            ['/my/path2/dir/.php_cs.cache', '/my/path2/dir/dir2/file', 'dir2/file'],
            ['dir/.php_cs.cache', '/my/path/dir/dir3/file', 'dir3/file'],
        ];
    }

    /**
     * @param null|string $cacheFile
     * @param string      $file
     * @param string      $expectedPathRelativeToFile
     *
     * @dataProvider provideGetDirectoryCases
     */
    public function testGetDirectory($cacheFile, $file, $expectedPathRelativeToFile)
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

        static::assertSame($expectedPathRelativeToFile, $directory->getRelativePathTo($file));
    }

    private function normalizePath($path)
    {
        return str_replace('/', \DIRECTORY_SEPARATOR, $path);
    }

    private static function assertSameRules(array $expected, array $actual, $message = '')
    {
        ksort($expected);
        ksort($actual);

        static::assertSame($expected, $actual, $message);
    }

    private function getFixtureDir()
    {
        return realpath(__DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.\DIRECTORY_SEPARATOR).'/';
    }

    private function createConfigurationResolver(array $options, Config $config = null, $cwdPath = '')
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
}
