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

use PhpCsFixer\Config;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Finder;
use PHPUnit\Framework\TestCase;
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
    /**
     * @var Config
     */
    private $config;

    protected function setUp()
    {
        $this->config = new Config();
    }

    protected function tearDown()
    {
        unset($this->config);
    }

    public function testSetOptionWithUndefinedOption()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidConfigurationException::class,
            '/^Unknown option name: "foo"\.$/'
        );

        new ConfigurationResolver(
            $this->config,
            ['foo' => 'bar'],
            ''
        );
    }

    public function testResolveProgressWithPositiveConfigAndPositiveOption()
    {
        $this->config->setHideProgress(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'format' => 'txt',
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
            ''
        );

        $this->assertSame('none', $resolver->getProgress());
    }

    public function testResolveProgressWithPositiveConfigAndNegativeOption()
    {
        $this->config->setHideProgress(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'format' => 'txt',
                'verbosity' => OutputInterface::VERBOSITY_NORMAL,
            ],
            ''
        );

        $this->assertSame('none', $resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndPositiveOption()
    {
        $this->config->setHideProgress(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'format' => 'txt',
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
            ''
        );

        $this->assertSame('run-in', $resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndNegativeOption()
    {
        $this->config->setHideProgress(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'format' => 'txt',
                'verbosity' => OutputInterface::VERBOSITY_NORMAL,
            ],
            ''
        );

        $this->assertSame('none', $resolver->getProgress());
    }

    /**
     * @param string $progressType
     *
     * @dataProvider provideProgressTypeCases
     */
    public function testResolveProgressWithPositiveConfigAndExplicitProgress($progressType)
    {
        $this->config->setHideProgress(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'format' => 'txt',
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
                'show-progress' => $progressType,
            ],
            ''
        );

        $this->assertSame($progressType, $resolver->getProgress());
    }

    /**
     * @param string $progressType
     *
     * @dataProvider provideProgressTypeCases
     */
    public function testResolveProgressWithNegativeConfigAndExplicitProgress($progressType)
    {
        $this->config->setHideProgress(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'format' => 'txt',
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
                'show-progress' => $progressType,
            ],
            ''
        );

        $this->assertSame($progressType, $resolver->getProgress());
    }

    public function provideProgressTypeCases()
    {
        return [
            ['none'],
            ['run-in'],
            ['estimating'],
        ];
    }

    public function testResolveProgressWithInvalidExplicitProgress()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'format' => 'txt',
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
                'show-progress' => 'foo',
            ],
            ''
        );

        $this->setExpectedException(
            \PhpCsFixer\ConfigurationException\InvalidConfigurationException::class,
            'The progress type "foo" is not defined, supported are "none", "run-in", "estimating".'
        );

        $resolver->getProgress();
    }

    public function testResolveConfigFileDefault()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            [],
            ''
        );

        $this->assertNull($resolver->getConfigFile());
        $this->assertInstanceOf(\PhpCsFixer\ConfigInterface::class, $resolver->getConfig());
    }

    public function testResolveConfigFileByPathOfFile()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_1';

        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [$dir.DIRECTORY_SEPARATOR.'foo.php']],
            ''
        );

        $this->assertSame($dir.DIRECTORY_SEPARATOR.'.php_cs.dist', $resolver->getConfigFile());
        $this->assertInstanceOf('Test1Config', $resolver->getConfig());
    }

    public function testResolveConfigFileSpecified()
    {
        $file = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_4/my.php_cs';

        $resolver = new ConfigurationResolver(
            $this->config,
            ['config' => $file],
            ''
        );

        $this->assertSame($file, $resolver->getConfigFile());
        $this->assertInstanceOf('Test4Config', $resolver->getConfig());
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
        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [$path]],
            $cwdPath
        );

        $this->assertSame($expectedFile, $resolver->getConfigFile());
        $this->assertInstanceOf($expectedClass, $resolver->getConfig());
    }

    public function provideResolveConfigFileDefaultCases()
    {
        $dirBase = $this->getFixtureDir();

        return [
            [
                $dirBase.'case_1'.DIRECTORY_SEPARATOR.'.php_cs.dist',
                'Test1Config',
                $dirBase.'case_1',
            ],
            [
                $dirBase.'case_2'.DIRECTORY_SEPARATOR.'.php_cs',
                'Test2Config',
                $dirBase.'case_2',
            ],
            [
                $dirBase.'case_3'.DIRECTORY_SEPARATOR.'.php_cs',
                'Test3Config',
                $dirBase.'case_3',
            ],
            [
                $dirBase.'case_6'.DIRECTORY_SEPARATOR.'.php_cs.dist',
                'Test6Config',
                $dirBase.'case_6'.DIRECTORY_SEPARATOR.'subdir',
                $dirBase.'case_6',
            ],
            [
                $dirBase.'case_6'.DIRECTORY_SEPARATOR.'.php_cs.dist',
                'Test6Config',
                $dirBase.'case_6'.DIRECTORY_SEPARATOR.'subdir/empty_file.php',
                $dirBase.'case_6',
            ],
        ];
    }

    public function testResolveConfigFileChooseFileWithInvalidFile()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidConfigurationException::class,
            '#^The config file: ".+[\/\\\]Fixtures[\/\\\]ConfigurationResolverConfigFile[\/\\\]case_5[\/\\\]\.php_cs\.dist" does not return a "PhpCsFixer\\\ConfigInterface" instance\. Got: "string"\.$#'
        );

        $dirBase = $this->getFixtureDir();

        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [$dirBase.'case_5']],
            ''
        );

        $resolver->getConfig();
    }

    public function testResolveConfigFileChooseFileWithInvalidFormat()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidConfigurationException::class,
            '/^The format "xls" is not defined, supported are "json", "junit", "txt", "xml"\.$/'
        );

        $dirBase = $this->getFixtureDir();

        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [$dirBase.'case_7']],
            ''
        );

        $resolver->getReporter();
    }

    public function testResolveConfigFileChooseFileWithPathArrayWithoutConfig()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidConfigurationException::class,
            '/^For multiple paths config parameter is required\.$/'
        );

        $dirBase = $this->getFixtureDir();

        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [$dirBase.'case_1/.php_cs.dist', $dirBase.'case_1/foo.php']],
            ''
        );

        $resolver->getConfig();
    }

    public function testResolveConfigFileChooseFileWithPathArrayAndConfig()
    {
        $dirBase = $this->getFixtureDir();

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'config' => $dirBase.'case_1/.php_cs.dist',
                'path' => [$dirBase.'case_1/.php_cs.dist', $dirBase.'case_1/foo.php'],
            ],
            ''
        );

        $this->assertInstanceOf(\PhpCsFixer\Console\ConfigurationResolver::class, $resolver);
    }

    public function testResolvePathRelativeA()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => ['Command']],
            __DIR__
        );

        $this->assertSame([__DIR__.DIRECTORY_SEPARATOR.'Command'], $resolver->getPath());
    }

    public function testResolvePathRelativeB()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [basename(__DIR__)]],
            dirname(__DIR__)
        );

        $this->assertSame([__DIR__], $resolver->getPath());
    }

    public function testResolvePathWithFileThatIsExcludedDirectlyOverridePathMode()
    {
        $this->config->getFinder()
            ->in(__DIR__)
            ->notPath(basename(__FILE__));

        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [__FILE__]],
            ''
        );

        $this->assertCount(1, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedDirectlyIntersectionPathMode()
    {
        $this->config->getFinder()
            ->in(__DIR__)
            ->notPath(basename(__FILE__));

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'path' => [__FILE__],
                'path-mode' => 'intersection',
            ],
            ''
        );

        $this->assertCount(0, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedByDirOverridePathMode()
    {
        $dir = dirname(__DIR__);
        $this->config->getFinder()
            ->in($dir)
            ->exclude(basename(__DIR__));

        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [__FILE__]],
            ''
        );

        $this->assertCount(1, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsExcludedByDirIntersectionPathMode()
    {
        $dir = dirname(__DIR__);
        $this->config->getFinder()
            ->in($dir)
            ->exclude(basename(__DIR__));

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'path-mode' => 'intersection',
                'path' => [__FILE__],
            ],
            ''
        );

        $this->assertCount(0, $resolver->getFinder());
    }

    public function testResolvePathWithFileThatIsNotExcluded()
    {
        $dir = __DIR__;
        $this->config->getFinder()
            ->in($dir)
            ->notPath('foo-'.basename(__FILE__));

        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [__FILE__]],
            ''
        );

        $this->assertCount(1, $resolver->getFinder());
    }

    /**
     * @param array|\Exception $expected
     * @param null|Finder      $configFinder
     * @param string           $pathMode
     * @param null|string      $config
     *
     * @dataProvider provideResolveIntersectionOfPathsCases
     */
    public function testResolveIntersectionOfPaths($expected, $configFinder, array $path, $pathMode, $config = null)
    {
        if ($expected instanceof \Exception) {
            $this->setExpectedException(get_class($expected));
        }

        if (null !== $configFinder) {
            $this->config->setFinder($configFinder);
        }

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'config' => $config,
                'path' => $path,
                'path-mode' => $pathMode,
            ],
            ''
        );

        $intersectionItems = array_map(
            function (\SplFileInfo $file) {
                return $file->getRealPath();
            },
            iterator_to_array($resolver->getFinder(), false)
        );

        sort($expected);
        sort($intersectionItems);

        $this->assertSame($expected, $intersectionItems);
    }

    public function provideResolveIntersectionOfPathsCases()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverPathsIntersection';
        $cb = function (array $items) use ($dir) {
            return array_map(
                function ($item) use ($dir) {
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
            'configured by config file, overriden by multiple files' => [
                $cb(['d/d1.php', 'd/d2.php']),
                null,
                [$dir.'/d/d1.php', $dir.'/d/d2.php'],
                'override',
                $dir.'/d/.php_cs',
            ],
            'configured by config file, intersected with multiple files' => [
                $cb(['d/d1.php', 'd/d2.php']),
                null,
                [$dir.'/d/d1.php', $dir.'/d/d2.php'],
                'intersection',
                $dir.'/d/.php_cs',
            ],
            'configured by config file, overriden by non-existing dir' => [
                new \LogicException(),
                null,
                [$dir.'/d/fff'],
                'override',
                $dir.'/d/.php_cs',
            ],
            'configured by config file, intersected with non-existing dir' => [
                new \LogicException(),
                null,
                [$dir.'/d/fff'],
                'intersection',
                $dir.'/d/.php_cs',
            ],
            'configured by config file, overriden by non-existing file' => [
                new \LogicException(),
                null,
                [$dir.'/d/fff.php'],
                'override',
                $dir.'/d/.php_cs',
            ],
            'configured by config file, intersected with non-existing file' => [
                new \LogicException(),
                null,
                [$dir.'/d/fff.php'],
                'intersection',
                $dir.'/d/.php_cs',
            ],
            'configured by config file, overriden by multiple files and dirs' => [
                $cb(['d/d1.php', 'd/e/de1.php', 'd/f/df1.php']),
                null,
                [$dir.'/d/d1.php', $dir.'/d/e', $dir.'/d/f/'],
                'override',
                $dir.'/d/.php_cs',
            ],
            'configured by config file, intersected with multiple files and dirs' => [
                $cb(['d/d1.php', 'd/e/de1.php', 'd/f/df1.php']),
                null,
                [$dir.'/d/d1.php', $dir.'/d/e', $dir.'/d/f/'],
                'intersection',
                $dir.'/d/.php_cs',
            ],
        ];
    }

    /**
     * @param array $options
     * @param bool  $expectedResult
     *
     * @dataProvider provideConfigFinderIsOverriddenCases
     */
    public function testConfigFinderIsOverridden(array $options, $expectedResult)
    {
        $resolver = new ConfigurationResolver($this->config, $options, '');

        $this->assertSame($expectedResult, $resolver->configFinderIsOverridden());

        $resolver = new ConfigurationResolver($this->config, $options, '');
        $resolver->getFinder();

        $this->assertSame($expectedResult, $resolver->configFinderIsOverridden());
    }

    public function provideConfigFinderIsOverriddenCases()
    {
        $root = __DIR__.'/../..';

        return [
            [
                [
                    'config' => $root.'/.php_cs.dist',
                ],
                false,
            ],
            [
                [
                    'config' => $root.'/.php_cs.dist',
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
                    'config' => $root.'/.php_cs.dist',
                    'path' => [$root.'/src'],
                    'path-mode' => ConfigurationResolver::PATH_MODE_INTERSECTION,
                ],
                false,
            ],
        ];
    }

    public function testResolveIsDryRunViaStdIn()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'dry-run' => false,
                'path' => ['-'],
            ],
            ''
        );

        $this->assertTrue($resolver->isDryRun());
    }

    public function testResolveIsDryRunViaNegativeOption()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            ['dry-run' => false],
            ''
        );

        $this->assertFalse($resolver->isDryRun());
    }

    public function testResolveIsDryRunViaPositiveOption()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            ['dry-run' => true],
            ''
        );

        $this->assertTrue($resolver->isDryRun());
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
        $this->config->setUsingCache($configValue);

        $resolver = new ConfigurationResolver(
            $this->config,
            ['using-cache' => $passed],
            ''
        );

        $this->assertSame($expected, $resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithPositiveConfigAndNoOption()
    {
        $this->config->setUsingCache(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            [],
            ''
        );

        $this->assertTrue($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndNoOption()
    {
        $this->config->setUsingCache(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            [],
            ''
        );

        $this->assertFalse($resolver->getUsingCache());
    }

    public function testResolveCacheFileWithoutConfigAndOption()
    {
        $default = $this->config->getCacheFile();

        $resolver = new ConfigurationResolver(
            $this->config,
            [],
            ''
        );

        $this->assertSame($default, $resolver->getCacheFile());
    }

    public function testResolveCacheFileWithConfig()
    {
        $cacheFile = 'foo/bar.baz';

        $this->config->setCacheFile($cacheFile);

        $resolver = new ConfigurationResolver(
            $this->config,
            [],
            ''
        );

        $this->assertSame($cacheFile, $resolver->getCacheFile());
    }

    public function testResolveCacheFileWithOption()
    {
        $cacheFile = 'bar.baz';

        $this->config->setCacheFile($cacheFile);

        $resolver = new ConfigurationResolver(
            $this->config,
            ['cache-file' => $cacheFile],
            ''
        );

        $this->assertSame($cacheFile, $resolver->getCacheFile());
    }

    public function testResolveCacheFileWithConfigAndOption()
    {
        $configCacheFile = 'foo/bar.baz';
        $optionCacheFile = 'bar.baz';

        $this->config->setCacheFile($configCacheFile);

        $resolver = new ConfigurationResolver(
            $this->config,
            ['cache-file' => $optionCacheFile],
            ''
        );

        $this->assertSame($optionCacheFile, $resolver->getCacheFile());
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
        $this->config->setRiskyAllowed($configValue);

        $resolver = new ConfigurationResolver(
            $this->config,
            ['allow-risky' => $passed],
            ''
        );

        $this->assertSame($expected, $resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndPositiveOption()
    {
        $this->config->setRiskyAllowed(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            ['allow-risky' => 'yes'],
            ''
        );

        $this->assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNegativeOption()
    {
        $this->config->setRiskyAllowed(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            ['allow-risky' => 'no'],
            ''
        );

        $this->assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndNoOption()
    {
        $this->config->setRiskyAllowed(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            [],
            ''
        );

        $this->assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNoOption()
    {
        $this->config->setRiskyAllowed(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            [],
            ''
        );

        $this->assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveRulesWithConfig()
    {
        $this->config->setRules([
            'braces' => true,
            'strict_comparison' => false,
        ]);

        $resolver = new ConfigurationResolver(
            $this->config,
            [],
            ''
        );

        $this->assertSameRules(
            [
                'braces' => true,
            ],
            $resolver->getRules()
        );
    }

    public function testResolveRulesWithOption()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            ['rules' => 'braces,-strict_comparison'],
            ''
        );

        $this->assertSameRules(
            [
                'braces' => true,
            ],
            $resolver->getRules()
        );
    }

    public function testResolveRulesWithUnknownRules()
    {
        $this->setExpectedException(
            \PhpCsFixer\ConfigurationException\InvalidConfigurationException::class,
            'The rules contain unknown fixers (bar).'
        );

        $resolver = new ConfigurationResolver(
            $this->config,
            ['rules' => 'braces,-bar'],
            ''
        );

        $resolver->getRules();
    }

    public function testResolveRulesWithConfigAndOption()
    {
        $this->config->setRules([
            'braces' => true,
            'strict_comparison' => false,
        ]);

        $resolver = new ConfigurationResolver(
            $this->config,
            ['rules' => 'blank_line_before_statement'],
            ''
        );

        $this->assertSameRules(
            [
                'blank_line_before_statement' => true,
            ],
            $resolver->getRules()
        );
    }

    public function testResolveCommandLineInputOverridesDefault()
    {
        $command = new FixCommand();
        $definition = $command->getDefinition();
        $arguments = $definition->getArguments();
        $this->assertCount(1, $arguments, 'Expected one argument, possibly test needs updating.');
        $this->assertArrayHasKey('path', $arguments);

        $options = $definition->getOptions();
        $this->assertSame(
            ['path-mode', 'allow-risky', 'config', 'dry-run', 'rules', 'using-cache', 'cache-file', 'diff', 'format', 'stop-on-violation', 'show-progress'],
            array_keys($options),
            'Expected options mismatch, possibly test needs updating.'
        );

        $resolver = new ConfigurationResolver(
            $this->config,
            [
                'path-mode' => 'intersection',
                'allow-risky' => 'yes',
                'config' => null,
                'dry-run' => true,
                'rules' => 'php_unit_construct',
                'using-cache' => false,
                'diff' => true,
                'format' => 'json',
                'stop-on-violation' => true,
            ],
            ''
        );

        $this->assertTrue($resolver->shouldStopOnViolation());
        $this->assertTrue($resolver->getRiskyAllowed());
        $this->assertTrue($resolver->isDryRun());
        $this->assertSame(['php_unit_construct' => true], $resolver->getRules());
        $this->assertFalse($resolver->getUsingCache());
        $this->assertNull($resolver->getCacheFile());
        $this->assertInstanceOf(\PhpCsFixer\Differ\SebastianBergmannDiffer::class, $resolver->getDiffer());
        $this->assertSame('json', $resolver->getReporter()->getFormat());
    }

    /**
     * @param string      $expected
     * @param bool|string $differConfig
     *
     * @dataProvider provideDifferCases
     */
    public function testResolveDiffer($expected, $differConfig)
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            ['diff' => $differConfig],
            ''
        );

        $this->assertInstanceOf($expected, $resolver->getDiffer());
    }

    public function provideDifferCases()
    {
        return [
            [
                \PhpCsFixer\Differ\NullDiffer::class,
                false,
            ],
            [
                \PhpCsFixer\Differ\SebastianBergmannDiffer::class,
                true,
            ],
        ];
    }

    public function testResolveConfigFileOverridesDefault()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_8';

        $resolver = new ConfigurationResolver(
            $this->config,
            ['path' => [$dir.DIRECTORY_SEPARATOR.'.php_cs']],
            ''
        );

        $this->assertTrue($resolver->getRiskyAllowed());
        $this->assertSame(['php_unit_construct' => true], $resolver->getRules());
        $this->assertFalse($resolver->getUsingCache());
        $this->assertNull($resolver->getCacheFile());
        $this->assertSame('xml', $resolver->getReporter()->getFormat());
    }

    /**
     * @group legacy
     * @expectedDeprecation Expected "yes" or "no" for option "allow-risky", other values are deprecated and support will be removed in 3.0. Got "yes please", this implicitly set the option to "false".
     */
    public function testDeprecationOfPassingOtherThanNoOrYes()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            ['allow-risky' => 'yes please'],
            ''
        );

        $this->assertFalse($resolver->getRiskyAllowed());
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
        $resolver = new ConfigurationResolver(
            $this->config,
            ['rules' => ''],
            ''
        );

        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidConfigurationException',
            '/^Empty rules value is not allowed\.$/'
        );

        $resolver->getRules();
    }

    private function assertSameRules(array $expected, array $actual, $message = '')
    {
        ksort($expected);
        ksort($actual);

        $this->assertSame($expected, $actual, $message);
    }

    private function getFixtureDir()
    {
        return realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.DIRECTORY_SEPARATOR).'/';
    }
}
