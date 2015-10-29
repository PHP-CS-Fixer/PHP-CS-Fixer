<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\ConfigurationResolver;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

/**
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ConfigurationResolverTest extends \PHPUnit_Framework_TestCase
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

        $this->config = $this->getMock('Symfony\CS\ConfigInterface');
        $this->resolver = new ConfigurationResolver();
        $this->resolver
            ->setAllFixers($fixer->getFixers())
            ->setConfig($this->config);
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
        $this->config->expects($this->once())->method('getLevel')
            ->will($this->returnValue(FixerInterface::PSR1_LEVEL));

        $this->resolver->resolve();

        $this->makeFixersTest(
            array_merge($this->fixersMap[FixerInterface::PSR0_LEVEL], $this->fixersMap[FixerInterface::PSR1_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithPositiveFixersConfig()
    {
        $this->config->expects($this->once())->method('getLevel')
            ->will($this->returnValue(FixerInterface::SYMFONY_LEVEL));

        $this->config->expects($this->once())->method('getFixers')
            ->will($this->returnValue(array('strict', 'strict_param')));

        $this->resolver->resolve();

        $expectedFixers = array_merge(
            $this->fixersMap[FixerInterface::PSR0_LEVEL],
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
        $this->config->expects($this->once())->method('getLevel')
            ->will($this->returnValue(FixerInterface::SYMFONY_LEVEL));

        $this->config->expects($this->once())->method('getFixers')
            ->will($this->returnValue(array('strict', '-include', 'strict_param')));

        $this->resolver->resolve();

        $expectedFixers = array_merge(
            $this->fixersMap[FixerInterface::PSR0_LEVEL],
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

    public function testResolveFixersWithLevelOption()
    {
        $this->resolver
            ->setOption('level', 'psr1')
            ->resolve();

        $this->makeFixersTest(
            array_merge($this->fixersMap[FixerInterface::PSR0_LEVEL], $this->fixersMap[FixerInterface::PSR1_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndLevelOption()
    {
        $this->config->expects($this->any())->method('getLevel')
            ->will($this->returnValue(FixerInterface::PSR2_LEVEL));

        $this->config->expects($this->any())->method('getFixers')
            ->will($this->returnValue(array('strict')));

        $this->resolver
            ->setOption('level', 'psr1')
            ->resolve();

        $this->makeFixersTest(
            array_merge($this->fixersMap[FixerInterface::PSR0_LEVEL], $this->fixersMap[FixerInterface::PSR1_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndPositiveFixersOption()
    {
        $this->config->expects($this->any())->method('getLevel')
            ->will($this->returnValue(FixerInterface::PSR2_LEVEL));

        $this->config->expects($this->any())->method('getFixers')
            ->will($this->returnValue(array('strict')));

        $this->resolver
            ->setOption('fixers', 'psr0')
            ->resolve();

        $this->makeFixersTest(
            array($this->fixersMap[FixerInterface::PSR0_LEVEL]['psr0']),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndNegativeFixersOption()
    {
        $this->config->expects($this->any())->method('getLevel')
            ->will($this->returnValue(FixerInterface::SYMFONY_LEVEL));

        $this->config->expects($this->any())->method('getFixers')
            ->will($this->returnValue(array('strict')));

        $this->resolver
            ->setOption('fixers', 'strict, -include,strict_param ')
            ->resolve();

        $expectedFixers = array_merge(
            $this->fixersMap[FixerInterface::PSR0_LEVEL],
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

    public function testResolveFixersWithLevelConfigAndFixersConfigAndLevelOptionsAndFixersOption()
    {
        $this->config->expects($this->any())->method('getLevel')
            ->will($this->returnValue(FixerInterface::PSR2_LEVEL));

        $this->config->expects($this->any())->method('getFixers')
            ->will($this->returnValue(array('concat_with_spaces')));

        $this->resolver
            ->setOption('level', 'symfony')
            ->setOption('fixers', 'strict, -include,strict_param ')
            ->resolve();

        $expectedFixers = array_merge(
            $this->fixersMap[FixerInterface::PSR0_LEVEL],
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
        $config = $this->getMock('Symfony\CS\Config\Config');
        $config->expects($this->any())->method('getHideProgress')
            ->will($this->returnValue(true));

        $this->resolver->setConfig($config);
        $this->resolver->setOption('progress', true);

        $this->assertFalse($this->resolver->getProgress());
    }

    public function testResolveProgressWithPositiveConfigAndNegativeOption()
    {
        $config = $this->getMock('Symfony\CS\Config\Config');
        $config->expects($this->any())->method('getHideProgress')
            ->will($this->returnValue(true));

        $this->resolver->setConfig($config);
        $this->resolver->setOption('progress', false);

        $this->assertFalse($this->resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndPositiveOption()
    {
        $config = $this->getMock('Symfony\CS\Config\Config');
        $config->expects($this->any())->method('getHideProgress')
            ->will($this->returnValue(false));

        $this->resolver->setConfig($config);
        $this->resolver->setOption('progress', true);

        $this->assertTrue($this->resolver->getProgress());
    }

    public function testResolveProgressWithNegativeConfigAndNegativeOption()
    {
        $config = $this->getMock('Symfony\CS\Config\Config');
        $config->expects($this->any())->method('getHideProgress')
            ->will($this->returnValue(false));

        $this->resolver->setConfig($config);
        $this->resolver->setOption('progress', false);

        $this->assertFalse($this->resolver->getProgress());
    }

    public function testResolveProgressRespectsConfigInterface()
    {
        $this->resolver->setOption('progress', true);

        $this->assertTrue($this->resolver->getProgress());
    }
}
