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

use Symfony\CS\Config;
use Symfony\CS\Console\ConfigurationResolver;
use Symfony\CS\Fixer;
use Symfony\CS\Test\AccessibleObject;

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

    protected function setUp()
    {
        $fixer = new Fixer();

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
            $this->config,
            $this->resolver
        );
    }

    public function testSetOption()
    {
        $this->resolver->setOption('path', '.');
        $property = AccessibleObject::create($this->resolver)->options;

        $this->assertSame('.', $property['path']);
    }

    /**
     * @expectedException              \Symfony\CS\ConfigurationException\InvalidConfigurationException
     * @expectedExceptionMessageRegExp /^Unknown option name: "foo"\.$/
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
        $property = AccessibleObject::create($this->resolver)->options;

        $this->assertSame('.', $property['path']);
        $this->assertSame('config.php_cs', $property['config-file']);
    }

    public function testCwd()
    {
        $this->resolver->setCwd('foo');
        $property = AccessibleObject::create($this->resolver)->cwd;

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

    public function testResolveConfigFileDefault()
    {
        $this->resolver
            ->resolve();

        $this->assertNull($this->resolver->getConfigFile());
        $this->assertInstanceOf('\\Symfony\\CS\\ConfigInterface', $this->resolver->getConfig());
    }

    public function testResolveConfigFileByPathOfFile()
    {
        $dir = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_1';

        $this->resolver
            ->setOption('path', $dir.DIRECTORY_SEPARATOR.'foo.php')
            ->resolve();

        $this->assertSame($dir.DIRECTORY_SEPARATOR.'.php_cs.dist', $this->resolver->getConfigFile());
        $this->assertInstanceOf('Test1Config', $this->resolver->getConfig());
    }

    public function testResolveConfigFileSpecified()
    {
        $file = __DIR__.'/../Fixtures/ConfigurationResolverConfigFile/case_4/my.php_cs';

        $this->resolver
            ->setOption('config-file', $file)
            ->resolve();

        $this->assertSame($file, $this->resolver->getConfigFile());
        $this->assertInstanceOf('Test4Config', $this->resolver->getConfig());
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
        );
    }

    /**
     * @expectedException              \Symfony\CS\ConfigurationException\InvalidConfigurationException
     * @expectedExceptionMessageRegExp /^The config file: ".+[\/\\]Fixtures[\/\\]ConfigurationResolverConfigFile[\/\\]case_5[\/\\].php_cs.dist" does not return a "Symfony\\CS\\ConfigInterface" instance\. Got: "string"\.$/
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

    public function testResolveAllowRiskyWithPositiveConfigAndPositiveOption()
    {
        $this->config->setRiskyAllowed(true);
        $this->resolver
            ->setOption('allow-risky', 'yes')
            ->resolve();

        $this->assertTrue($this->config->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndNegativeOption()
    {
        $this->config->setRiskyAllowed(true);
        $this->resolver
            ->setOption('allow-risky', 'no')
            ->resolve();

        $this->assertFalse($this->config->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndPositiveOption()
    {
        $this->config->setRiskyAllowed(false);
        $this->resolver
            ->setOption('allow-risky', 'yes')
            ->resolve();

        $this->assertTrue($this->config->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNegativeOption()
    {
        $this->config->setRiskyAllowed(false);
        $this->resolver
            ->setOption('allow-risky', 'no')
            ->resolve();

        $this->assertFalse($this->config->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithPositiveConfigAndNoOption()
    {
        $this->config->setRiskyAllowed(true);
        $this->resolver
            ->resolve();

        $this->assertTrue($this->config->getRiskyAllowed());
    }

    public function testResolveAllowRiskyWithNegativeConfigAndNoOption()
    {
        $this->config->setRiskyAllowed(false);
        $this->resolver
            ->resolve();

        $this->assertFalse($this->config->getRiskyAllowed());
    }

    public function testResolveRulesWithConfig()
    {
        $this->config->setRules(array(
            'braces' => true,
            'strict' => false,
        ));

        $this->resolver->resolve();

        $this->assertSameRules(
            array(
                'braces' => true,
            ),
            $this->resolver->getRules()
        );
    }

    public function testResolveRulesWithOption()
    {
        $this->resolver->setOption('rules', 'braces,-strict');

        $this->resolver->resolve();

        $this->assertSameRules(
            array(
                'braces' => true,
            ),
            $this->resolver->getRules()
        );
    }

    public function testResolveRulesWithConfigAndOption()
    {
        $this->config->setRules(array(
            'braces' => true,
            'strict' => false,
        ));

        $this->resolver->setOption('rules', 'return');

        $this->resolver->resolve();

        $this->assertSameRules(
            array(
                'return' => true,
            ),
            $this->resolver->getRules()
        );
    }

    private function assertSameRules(array $expected, array $actual, $message = '')
    {
        ksort($expected);
        ksort($actual);

        $this->assertSame($expected, $actual, $message);
    }
}
