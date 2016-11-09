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
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Finder;

/**
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ConfigurationResolverTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @expectedException              \PhpCsFixer\ConfigurationException\InvalidConfigurationException
     * @expectedExceptionMessageRegExp /^Unknown option name: "foo"\.$/
     */
    public function testSetOptionWithUndefinedOption()
    {
        new ConfigurationResolver(
            $this->config,
            array('foo' => 'bar'),
            ''
        );
    }

    public function testResolveProgressWithPositiveConfigAndPositiveOption()
    {
        $this->config->setHideProgress(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('progress' => true),
            ''
        );

        $this->assertFalse($resolver->getProgress());
    }

    public function testResolveProgressWithPositiveConfigAndNegativeOption()
    {
        $this->config->setHideProgress(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('progress' => false),
            ''
        );

        $this->assertFalse($resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndPositiveOption()
    {
        $this->config->setHideProgress(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('progress' => true),
            ''
        );

        $this->assertTrue($resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndNegativeOption()
    {
        $this->config->setHideProgress(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('progress' => false),
            ''
        );

        $this->assertFalse($resolver->getProgress());
    }

    public function testResolveConfigFileDefault()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            array(),
            ''
        );

        $this->assertNull($resolver->getConfigFile());
        $this->assertInstanceOf('\\PhpCsFixer\\ConfigInterface', $resolver->getConfig());
    }

    public function testResolveConfigFileByPathOfFile()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_1';

        $resolver = new ConfigurationResolver(
            $this->config,
            array('path' => array($dir.DIRECTORY_SEPARATOR.'foo.php')),
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
            array('config' => $file),
            ''
        );

        $this->assertSame($file, $resolver->getConfigFile());
        $this->assertInstanceOf('Test4Config', $resolver->getConfig());
    }

    /**
     * @dataProvider provideResolveConfigFileDefaultCases
     */
    public function testResolveConfigFileChooseFile($expectedFile, $expectedClass, $path, $cwdPath = null)
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            array('path' => array($path)),
            $cwdPath
        );

        $this->assertSame($expectedFile, $resolver->getConfigFile());
        $this->assertInstanceOf($expectedClass, $resolver->getConfig());
    }

    public function provideResolveConfigFileDefaultCases()
    {
        $dirBase = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.DIRECTORY_SEPARATOR;

        return array(
            array(
                $dirBase.'case_1'.DIRECTORY_SEPARATOR.'.php_cs.dist',
                'Test1Config',
                $dirBase.'case_1',
            ),
            array(
                $dirBase.'case_2'.DIRECTORY_SEPARATOR.'.php_cs',
                'Test2Config',
                $dirBase.'case_2',
            ),
            array(
                $dirBase.'case_3'.DIRECTORY_SEPARATOR.'.php_cs',
                'Test3Config',
                $dirBase.'case_3',
            ),
            array(
                $dirBase.'case_6'.DIRECTORY_SEPARATOR.'.php_cs.dist',
                'Test6Config',
                $dirBase.'case_6'.DIRECTORY_SEPARATOR.'subdir',
                $dirBase.'case_6',
            ),
            array(
                $dirBase.'case_6'.DIRECTORY_SEPARATOR.'.php_cs.dist',
                'Test6Config',
                $dirBase.'case_6'.DIRECTORY_SEPARATOR.'subdir/empty_file.php',
                $dirBase.'case_6',
            ),
        );
    }

    /**
     * @expectedException              \PhpCsFixer\ConfigurationException\InvalidConfigurationException
     * @expectedExceptionMessageRegExp /^The config file: ".+[\/\\]Fixtures[\/\\]ConfigurationResolverConfigFile[\/\\]case_5[\/\\].php_cs.dist" does not return a "PhpCsFixer\\ConfigInterface" instance\. Got: "string"\.$/
     */
    public function testResolveConfigFileChooseFileWithInvalidFile()
    {
        $dirBase = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.DIRECTORY_SEPARATOR);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('path' => array($dirBase.'/case_5')),
            ''
        );

        $resolver->getConfig();
    }

    /**
     * @expectedException              \PhpCsFixer\ConfigurationException\InvalidConfigurationException
     * @expectedExceptionMessageRegExp /^For multiple paths config parameter is required.$/
     */
    public function testResolveConfigFileChooseFileWithPathArrayWithoutConfig()
    {
        $dirBase = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.DIRECTORY_SEPARATOR);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('path' => array($dirBase.'/case_1/.php_cs.dist', $dirBase.'/case_1/foo.php')),
            ''
        );

        $resolver->getConfig();
    }

    public function testResolveConfigFileChooseFileWithPathArrayAndConfig()
    {
        $dirBase = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.DIRECTORY_SEPARATOR);

        new ConfigurationResolver(
            $this->config,
            array(
                'config' => $dirBase.'/case_1/.php_cs.dist',
                'path' => array($dirBase.'/case_1/.php_cs.dist', $dirBase.'/case_1/foo.php'),
            ),
            ''
        );
    }

    public function testResolvePathRelativeA()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            array('path' => array('Command')),
            __DIR__
        );

        $this->assertSame(array(__DIR__.DIRECTORY_SEPARATOR.'Command'), $resolver->getPath());
    }

    public function testResolvePathRelativeB()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            array('path' => array(basename(__DIR__))),
            dirname(__DIR__)
        );

        $this->assertSame(array(__DIR__), $resolver->getPath());
    }

    public function testResolvePathWithFileThatIsExcludedDirectlyOverridePathMode()
    {
        $this->config->getFinder()
            ->in(__DIR__)
            ->notPath(basename(__FILE__));

        $resolver = new ConfigurationResolver(
            $this->config,
            array('path' => array(__FILE__)),
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
            array(
                'path' => array(__FILE__),
                'path-mode' => 'intersection',
            ),
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
            array('path' => array(__FILE__)),
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
            array(
                'path-mode' => 'intersection',
                'path' => array(__FILE__),
            ),
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
            array('path' => array(__FILE__)),
            ''
        );

        $this->assertCount(1, $resolver->getFinder());
    }

    /**
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
            array(
                'config' => $config,
                'path' => $path,
                'path-mode' => $pathMode,
            ),
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

        return array(
            'no path at all' => array(
                new \LogicException(),
                Finder::create(),
                array(),
                'override',
            ),
            'configured only by finder' => array(
                // don't override if the argument is empty
                $cb(array('a1.php', 'a2.php', 'b/b1.php', 'b/b2.php', 'b_b/b_b1.php', 'c/c1.php', 'c/d/cd1.php', 'd/d1.php', 'd/d2.php', 'd/e/de1.php', 'd/f/df1.php')),
                Finder::create()
                    ->in($dir),
                array(),
                'override',
            ),
            'configured only by argument' => array(
                $cb(array('a1.php', 'a2.php', 'b/b1.php', 'b/b2.php', 'b_b/b_b1.php', 'c/c1.php', 'c/d/cd1.php', 'd/d1.php', 'd/d2.php', 'd/e/de1.php', 'd/f/df1.php')),
                Finder::create(),
                array($dir),
                'override',
            ),
            'configured by finder, intersected with empty argument' => array(
                array(),
                Finder::create()
                    ->in($dir),
                array(),
                'intersection',
            ),
            'configured by finder, intersected with dir' => array(
                $cb(array('c/c1.php', 'c/d/cd1.php')),
                Finder::create()
                    ->in($dir),
                array($dir.'/c'),
                'intersection',
            ),
            'configured by finder, intersected with file' => array(
                $cb(array('c/c1.php')),
                Finder::create()
                    ->in($dir),
                array($dir.'/c/c1.php'),
                'intersection',
            ),
            'finder points to one dir while argument to another, not connected' => array(
                array(),
                Finder::create()
                    ->in($dir.'/b'),
                array($dir.'/c'),
                'intersection',
            ),
            'finder with excluded dir, intersected with excluded file' => array(
                array(),
                Finder::create()
                    ->in($dir)
                    ->exclude('c'),
                array($dir.'/c/d/cd1.php'),
                'intersection',
            ),
            'finder with excluded dir, intersected with dir containing excluded one' => array(
                $cb(array('c/c1.php')),
                Finder::create()
                    ->in($dir)
                    ->exclude('c/d'),
                array($dir.'/c'),
                'intersection',
            ),
            'finder with excluded file, intersected with dir containing excluded one' => array(
                $cb(array('c/d/cd1.php')),
                Finder::create()
                    ->in($dir)
                    ->notPath('c/c1.php'),
                array($dir.'/c'),
                'intersection',
            ),
            'configured by finder, intersected with non-existing path' => array(
                new \LogicException(),
                Finder::create()
                    ->in($dir),
                array('non_existing_dir'),
                'intersection',
            ),
            'configured by config file, overriden by multiple files' => array(
                $cb(array('d/d1.php', 'd/d2.php')),
                null,
                array($dir.'/d/d1.php', $dir.'/d/d2.php'),
                'override',
                $dir.'/d/.php_cs',
            ),
            'configured by config file, intersected with multiple files' => array(
                $cb(array('d/d1.php', 'd/d2.php')),
                null,
                array($dir.'/d/d1.php', $dir.'/d/d2.php'),
                'intersection',
                $dir.'/d/.php_cs',
            ),
            'configured by config file, overriden by non-existing dir' => array(
                new \LogicException(),
                null,
                array($dir.'/d/fff'),
                'override',
                $dir.'/d/.php_cs',
            ),
            'configured by config file, intersected with non-existing dir' => array(
                new \LogicException(),
                null,
                array($dir.'/d/fff'),
                'intersection',
                $dir.'/d/.php_cs',
            ),
            'configured by config file, overriden by non-existing file' => array(
                new \LogicException(),
                null,
                array($dir.'/d/fff.php'),
                'override',
                $dir.'/d/.php_cs',
            ),
            'configured by config file, intersected with non-existing file' => array(
                new \LogicException(),
                null,
                array($dir.'/d/fff.php'),
                'intersection',
                $dir.'/d/.php_cs',
            ),
            'configured by config file, overriden by multiple files and dirs' => array(
                $cb(array('d/d1.php', 'd/e/de1.php', 'd/f/df1.php')),
                null,
                array($dir.'/d/d1.php', $dir.'/d/e', $dir.'/d/f/'),
                'override',
                $dir.'/d/.php_cs',
            ),
            'configured by config file, intersected with multiple files and dirs' => array(
                $cb(array('d/d1.php', 'd/e/de1.php', 'd/f/df1.php')),
                null,
                array($dir.'/d/d1.php', $dir.'/d/e', $dir.'/d/f/'),
                'intersection',
                $dir.'/d/.php_cs',
            ),
        );
    }

    public function testResolveIsDryRunViaStdIn()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            array(
                'dry-run' => false,
                'path' => array('-'),
            ),
            ''
        );

        $this->assertTrue($resolver->isDryRun());
    }

    public function testResolveIsDryRunViaNegativeOption()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            array('dry-run' => false),
            ''
        );

        $this->assertFalse($resolver->isDryRun());
    }

    public function testResolveIsDryRunViaPositiveOption()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            array('dry-run' => true),
            ''
        );

        $this->assertTrue($resolver->isDryRun());
    }

    public function testResolveUsingCacheWithPositiveConfigAndPositiveOption()
    {
        $this->config->setUsingCache(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('using-cache' => 'yes'),
            ''
        );

        $this->assertTrue($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithPositiveConfigAndNegativeOption()
    {
        $this->config->setUsingCache(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('using-cache' => 'no'),
            ''
        );

        $this->assertFalse($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndPositiveOption()
    {
        $this->config->setUsingCache(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('using-cache' => 'yes'),
            ''
        );

        $this->assertTrue($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndNegativeOption()
    {
        $this->config->setUsingCache(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('using-cache' => 'no'),
            ''
        );

        $this->assertFalse($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithPositiveConfigAndNoOption()
    {
        $this->config->setUsingCache(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            array(),
            ''
        );

        $this->assertTrue($resolver->getUsingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndNoOption()
    {
        $this->config->setUsingCache(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            array(),
            ''
        );

        $this->assertFalse($resolver->getUsingCache());
    }

    public function testResolveCacheFileWithoutConfigAndOption()
    {
        $default = $this->config->getCacheFile();

        $resolver = new ConfigurationResolver(
            $this->config,
            array(),
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
            array(),
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
            array('cache-file' => $cacheFile),
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
            array('cache-file' => $optionCacheFile),
            ''
        );

        $this->assertSame($optionCacheFile, $resolver->getCacheFile());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndPositiveOption()
    {
        $this->config->setRiskyAllowed(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('allow-risky' => 'yes'),
            ''
        );

        $this->assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndNegativeOption()
    {
        $this->config->setRiskyAllowed(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('allow-risky' => 'no'),
            ''
        );

        $this->assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndPositiveOption()
    {
        $this->config->setRiskyAllowed(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('allow-risky' => 'yes'),
            ''
        );

        $this->assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNegativeOption()
    {
        $this->config->setRiskyAllowed(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            array('allow-risky' => 'no'),
            ''
        );

        $this->assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndNoOption()
    {
        $this->config->setRiskyAllowed(true);

        $resolver = new ConfigurationResolver(
            $this->config,
            array(),
            ''
        );

        $this->assertTrue($resolver->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNoOption()
    {
        $this->config->setRiskyAllowed(false);

        $resolver = new ConfigurationResolver(
            $this->config,
            array(),
            ''
        );

        $this->assertFalse($resolver->getRiskyAllowed());
    }

    public function testResolveRulesWithConfig()
    {
        $this->config->setRules(array(
            'braces' => true,
            'strict_comparison' => false,
        ));

        $resolver = new ConfigurationResolver(
            $this->config,
            array(),
            ''
        );

        $this->assertSameRules(
            array(
                'braces' => true,
            ),
            $resolver->getRules()
        );
    }

    public function testResolveRulesWithOption()
    {
        $resolver = new ConfigurationResolver(
            $this->config,
            array('rules' => 'braces,-strict'),
            ''
        );

        $this->assertSameRules(
            array(
                'braces' => true,
            ),
            $resolver->getRules()
        );
    }

    public function testResolveRulesWithConfigAndOption()
    {
        $this->config->setRules(array(
            'braces' => true,
            'strict_comparison' => false,
        ));

        $resolver = new ConfigurationResolver(
            $this->config,
            array('rules' => 'blank_line_before_return'),
            ''
        );

        $this->assertSameRules(
            array(
                'blank_line_before_return' => true,
            ),
            $resolver->getRules()
        );
    }

    private function assertSameRules(array $expected, array $actual, $message = '')
    {
        ksort($expected);
        ksort($actual);

        $this->assertSame($expected, $actual, $message);
    }
}
