<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Console;

use Symfony\CS\Config\Config;
use Symfony\CS\Console\ConfigurationResolver;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

/**
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ConfigurationResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $config;
    protected $resolver;
    protected $fixersMap;

    protected function setUp()
    {
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();
        $fixer->registerBuiltInConfigs();

        $fixersMap = array();

        foreach ($fixer->getFixers() as $singleFixer) {
            $level = $singleFixer->getLevel();

            if (!isset($fixersMap[$level])) {
                $fixersMap[$level] = array();
            }

            $fixersMap[$level][$singleFixer->getName()] = $singleFixer;
        }

        $this->fixersMap = $fixersMap;

        $this->config = new Config();
        $this->resolver = new ConfigurationResolver();
        $this->resolver
            ->setDefaultConfig($this->config)
            ->setFixer($fixer)
        ;
    }

    protected function tearDown()
    {
        unset(
            $this->fixersMap,
            $this->config,
            $this->resolver
        );
    }

    public function testSetOption()
    {
        $this->resolver->setOption('path', '.');

        $classReflection = new \ReflectionClass($this->resolver);
        $propertyReflection = $classReflection->getProperty('options');
        $propertyReflection->setAccessible(true);
        $property = $propertyReflection->getValue($this->resolver);

        $this->assertSame('.', $property['path']);
    }

    /**
     * @expectedException              \OutOfBoundsException
     * @expectedExceptionMessageRegExp /Unknown option name: "foo"/
     */
    public function testSetOptionWithUndefinedOption()
    {
        $this->resolver->setOption('foo', 'bar');
    }

    public function testSetOptions()
    {
        $this->resolver->setOptions(array(
            'path' => '.',
            'config-file' => 'config.php_cs',
        ));

        $classReflection = new \ReflectionClass($this->resolver);
        $propertyReflection = $classReflection->getProperty('options');
        $propertyReflection->setAccessible(true);
        $property = $propertyReflection->getValue($this->resolver);

        $this->assertSame('.', $property['path']);
        $this->assertSame('config.php_cs', $property['config-file']);
    }

    public function testCwd()
    {
        $this->resolver->setCwd('foo');

        $classReflection = new \ReflectionClass($this->resolver);
        $propertyReflection = $classReflection->getProperty('cwd');
        $propertyReflection->setAccessible(true);
        $property = $propertyReflection->getValue($this->resolver);

        $this->assertSame('foo', $property);
    }

    protected function makeFixersTest($expectedFixers, $resolvedFixers)
    {
        $this->assertCount(count($expectedFixers), $resolvedFixers);

        foreach ($expectedFixers as $fixer) {
            $this->assertContains($fixer, $resolvedFixers);
        }
    }

    public function testResolveFixersReturnsEmptyArrayByDefault()
    {
        $this->makeFixersTest(array(), $this->resolver->getFixers());
    }

    public function testResolveFixersWithLevelConfig()
    {
        $this->config->level(FixerInterface::PSR2_LEVEL);

        $this->resolver->resolve();

        $this->makeFixersTest(
            array_merge($this->fixersMap[FixerInterface::PSR1_LEVEL], $this->fixersMap[FixerInterface::PSR2_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithPositiveFixersConfig()
    {
        $this->config->level(FixerInterface::SYMFONY_LEVEL);
        $this->config->fixers(array('strict', 'strict_param'));

        $this->resolver->resolve();

        $expectedFixers = array_merge(
            $this->fixersMap[FixerInterface::PSR1_LEVEL],
            $this->fixersMap[FixerInterface::PSR2_LEVEL],
            $this->fixersMap[FixerInterface::SYMFONY_LEVEL],
            array($this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict'], $this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict_param'])
        );

        $this->makeFixersTest(
            $expectedFixers,
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithNegativeFixersConfig()
    {
        $this->config->level(FixerInterface::SYMFONY_LEVEL);
        $this->config->fixers(array('strict', '-include', 'strict_param'));

        $this->resolver->resolve();

        $expectedFixers = array_merge(
            $this->fixersMap[FixerInterface::PSR1_LEVEL],
            $this->fixersMap[FixerInterface::PSR2_LEVEL],
            $this->fixersMap[FixerInterface::SYMFONY_LEVEL],
            array($this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict'], $this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict_param'])
        );

        foreach ($expectedFixers as $key => $fixer) {
            if ('include' === $fixer->getName()) {
                unset($expectedFixers[$key]);
                break;
            }
        }

        $this->makeFixersTest(
            $expectedFixers,
            $this->resolver->getFixers()
        );
    }

    /**
     * @expectedException              \InvalidArgumentException
     * @expectedExceptionMessageRegExp /The level "foo" is not defined./
     */
    public function testResolveFixersWithInvalidLevelOption()
    {
        $this->resolver
            ->setOption('level', 'foo')
            ->resolve()
        ;
    }

    public function testResolveFixersWithLevelOption()
    {
        $this->resolver
            ->setOption('level', 'psr2')
            ->resolve()
        ;

        $this->makeFixersTest(
            array_merge($this->fixersMap[FixerInterface::PSR1_LEVEL], $this->fixersMap[FixerInterface::PSR2_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndLevelOption()
    {
        $this->config
            ->level(FixerInterface::SYMFONY_LEVEL)
            ->fixers(array('strict'))
        ;
        $this->resolver
            ->setOption('level', 'psr2')
            ->resolve()
        ;

        $this->makeFixersTest(
            array_merge($this->fixersMap[FixerInterface::PSR1_LEVEL], $this->fixersMap[FixerInterface::PSR2_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndPositiveFixersOption()
    {
        $this->config
            ->level(FixerInterface::PSR2_LEVEL)
            ->fixers(array('strict'))
        ;
        $this->resolver
            ->setOption('fixers', 'short_tag')
            ->resolve()
        ;

        $this->makeFixersTest(
            array($this->fixersMap[FixerInterface::PSR1_LEVEL]['short_tag']),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndNegativeFixersOption()
    {
        $this->config
            ->level(FixerInterface::SYMFONY_LEVEL)
            ->fixers(array('strict'))
        ;
        $this->resolver
            ->setOption('fixers', 'strict, -include,strict_param ')
            ->resolve()
        ;

        $expectedFixers = array(
            $this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict'],
            $this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict_param'],
        );

        $this->makeFixersTest(
            $expectedFixers,
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndLevelOptionsAndFixersOption()
    {
        $this->config
            ->level(FixerInterface::PSR2_LEVEL)
            ->fixers(array('concat_with_spaces'))
        ;
        $this->resolver
            ->setOption('level', 'symfony')
            ->setOption('fixers', 'strict, -include,strict_param ')
            ->resolve()
        ;

        $expectedFixers = array_merge(
            $this->fixersMap[FixerInterface::PSR1_LEVEL],
            $this->fixersMap[FixerInterface::PSR2_LEVEL],
            $this->fixersMap[FixerInterface::SYMFONY_LEVEL],
            array($this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict'], $this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict_param'])
        );

        foreach ($expectedFixers as $key => $fixer) {
            if ($fixer->getName() === 'include') {
                unset($expectedFixers[$key]);
                break;
            }
        }

        $this->makeFixersTest(
            $expectedFixers,
            $this->resolver->getFixers()
        );
    }

    public function testResolveProgressWithPositiveConfigAndPositiveOption()
    {
        $this->config->hideProgress(true);
        $this->resolver
            ->setOption('progress', true)
            ->resolve()
        ;

        $this->assertFalse($this->resolver->getProgress());
    }

    public function testResolveProgressWithPositiveConfigAndNegativeOption()
    {
        $this->config->hideProgress(true);
        $this->resolver
            ->setOption('progress', false)
            ->resolve()
        ;

        $this->assertFalse($this->resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndPositiveOption()
    {
        $this->config->hideProgress(false);
        $this->resolver
            ->setOption('progress', true)
            ->resolve()
        ;

        $this->assertTrue($this->resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndNegativeOption()
    {
        $this->config->hideProgress(false);
        $this->resolver
            ->setOption('progress', false)
            ->resolve()
        ;

        $this->assertFalse($this->resolver->getProgress());
    }

    /**
     * @dataProvider provideResolveConfigByNameCases
     */
    public function testResolveConfigByName($expected, $name)
    {
        $this->resolver
            ->setOption('config', $name)
            ->resolve()
        ;

        $this->assertInstanceOf($expected, $this->resolver->getConfig());
    }

    public function provideResolveConfigByNameCases()
    {
        return array(
            array('\\Symfony\\CS\\Config\\Config', 'default'),
            array('\\Symfony\\CS\\Config\\MagentoConfig', 'magento'),
            array('\\Symfony\\CS\\Config\\Symfony23Config', 'sf23'),
        );
    }

    /**
     * @expectedException              \InvalidArgumentException
     * @expectedExceptionMessageRegExp /The configuration "\w+" is not defined/
     */
    public function testResolveConfigByNameThatDoesntExists()
    {
        $this->resolver
            ->setOption('config', 'NON_EXISTING_CONFIG')
            ->resolve()
        ;
    }

    public function testResolveConfigFileDefault()
    {
        $this->resolver
            ->resolve();

        $this->assertNull($this->resolver->getConfigFile());
        $this->assertInstanceOf('\\Symfony\\CS\\Config\\Config', $this->resolver->getConfig());
    }

    public function testResolveConfigFileByPathOfFile()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_1';

        $this->resolver
            ->setOption('path', $dir.DIRECTORY_SEPARATOR.'foo.php')
            ->resolve();

        $this->assertSame($dir.DIRECTORY_SEPARATOR.'.php_cs.dist', $this->resolver->getConfigFile());
        $this->assertInstanceOf('\\Symfony\\CS\\Config\\MagentoConfig', $this->resolver->getConfig());
    }

    public function testResolveConfigFileSpecified()
    {
        $file = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_4/my.php_cs';

        $this->resolver
            ->setOption('config-file', $file)
            ->resolve();

        $this->assertSame($file, $this->resolver->getConfigFile());
        $this->assertInstanceOf('\\Symfony\\CS\\Config\\MagentoConfig', $this->resolver->getConfig());
    }

    /**
     * @dataProvider provideResolveConfigFileDefaultCases
     */
    public function testResolveConfigFileChooseFile($expectedFile, $expectedClass, $path)
    {
        $this->resolver
            ->setOption('path', $path)
            ->resolve();

        $this->assertSame($expectedFile, $this->resolver->getConfigFile());
        $this->assertInstanceOf($expectedClass, $this->resolver->getConfig());
    }

    public function provideResolveConfigFileDefaultCases()
    {
        $dirBase = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.DIRECTORY_SEPARATOR;

        return array(
            array(
                $dirBase.'case_1'.DIRECTORY_SEPARATOR.'.php_cs.dist',
                '\\Symfony\\CS\\Config\\MagentoConfig',
                $dirBase.'case_1',
            ),
            array(
                $dirBase.'case_2'.DIRECTORY_SEPARATOR.'.php_cs',
                '\\Symfony\\CS\\Config\\MagentoConfig',
                $dirBase.'case_2',
            ),
            array(
                $dirBase.'case_3'.DIRECTORY_SEPARATOR.'.php_cs',
                '\\Symfony\\CS\\Config\\MagentoConfig',
                $dirBase.'case_3',
            ),
        );
    }

    /**
     * @expectedException              \UnexpectedValueException
     * @expectedExceptionMessageRegExp /The config file: ".+[\/\\]Tests[\/\\]Fixtures[\/\\]ConfigurationResolverConfigFile[\/\\]case_5[\/\\].php_cs.dist" does not return a "Symfony\\CS\\Config\\Config" instance\. Got: "string"\./
     */
    public function testResolveConfigFileChooseFileWithInvalidFile()
    {
        $dirBase = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'ConfigurationResolverConfigFile'.DIRECTORY_SEPARATOR;
        $dirBase = realpath($dirBase);
        $this->resolver
            ->setOption('path', $dirBase.'/case_5')
            ->resolve();
    }

    public function testResolvePathRelative()
    {
        $this->resolver
            ->setCwd(__DIR__)
            ->setOption('path', 'Foo'.DIRECTORY_SEPARATOR.'Bar')
            ->resolve();

        $this->assertSame(__DIR__.DIRECTORY_SEPARATOR.'Foo'.DIRECTORY_SEPARATOR.'Bar', $this->resolver->getPath());
    }

    public function testResolveIsDryRunViaStdIn()
    {
        $this->resolver
            ->setOption('path', '-')
            ->setOption('dry-run', false)
            ->resolve();

        $this->assertTrue($this->resolver->isDryRun());
    }

    public function testResolveIsDryRunViaNegativeOption()
    {
        $this->resolver
            ->setOption('dry-run', false)
            ->resolve();

        $this->assertFalse($this->resolver->isDryRun());
    }

    public function testResolveIsDryRunViaPositiveOption()
    {
        $this->resolver
            ->setOption('dry-run', true)
            ->resolve();

        $this->assertTrue($this->resolver->isDryRun());
    }

    public function testResolveUsingCacheWithPositiveConfigAndPositiveOption()
    {
        $this->config->setUsingCache(true);
        $this->resolver
            ->setOption('using-cache', 'yes')
            ->resolve();

        $this->assertTrue($this->config->usingCache());
    }

    public function testResolveUsingCacheWithPositiveConfigAndNegativeOption()
    {
        $this->config->setUsingCache(true);
        $this->resolver
            ->setOption('using-cache', 'no')
            ->resolve();

        $this->assertFalse($this->config->usingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndPositiveOption()
    {
        $this->config->setUsingCache(false);
        $this->resolver
            ->setOption('using-cache', 'yes')
            ->resolve();

        $this->assertTrue($this->config->usingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndNegativeOption()
    {
        $this->config->setUsingCache(false);
        $this->resolver
            ->setOption('using-cache', 'no')
            ->resolve();

        $this->assertFalse($this->config->usingCache());
    }

    public function testResolveUsingCacheWithPositiveConfigAndNoOption()
    {
        $this->config->setUsingCache(true);
        $this->resolver
            ->resolve();

        $this->assertTrue($this->config->usingCache());
    }

    public function testResolveUsingCacheWithNegativeConfigAndNoOption()
    {
        $this->config->setUsingCache(false);
        $this->resolver
            ->resolve();

        $this->assertFalse($this->config->usingCache());
    }

    public function testResolveCacheFileWithoutConfigAndOption()
    {
        $default = $this->config->getCacheFile();

        $this->resolver->resolve();

        $this->assertSame($default, $this->config->getCacheFile());
    }

    public function testResolveCacheFileWithConfig()
    {
        $cacheFile = 'foo/bar.baz';

        $this->config->setCacheFile($cacheFile);

        $this->resolver->resolve();

        $this->assertSame($cacheFile, $this->config->getCacheFile());
    }

    public function testResolveCacheFileWithOption()
    {
        $cacheFile = 'bar.baz';

        $this->config->setCacheFile($cacheFile);
        $this->resolver->setOption('cache-file', $cacheFile);

        $this->resolver->resolve();

        $this->assertSame($cacheFile, $this->config->getCacheFile());
    }

    public function testResolveCacheFileWithConfigAndOption()
    {
        $configCacheFile = 'foo/bar.baz';
        $optionCacheFile = 'bar.baz';

        $this->config->setCacheFile($configCacheFile);
        $this->resolver->setOption('cache-file', $optionCacheFile);

        $this->resolver->resolve();

        $this->assertSame($optionCacheFile, $this->config->getCacheFile());
    }
}
