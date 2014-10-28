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

use Symfony\CS\Config\Config;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\FixersResolver;

/**
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class FixersResolverTest extends \PHPUnit_Framework_TestCase
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
        $this->resolver = new FixersResolver($fixer->getFixers());
        $this->resolver->setConfig($this->config);
    }

    protected function makeTest($expectedFixers, $resolvedFixers)
    {
        $this->assertCount(count($expectedFixers), $resolvedFixers);

        foreach ($expectedFixers as $fixer) {
            $this->assertContains($fixer, $resolvedFixers);
        }
    }

    public function testResolveFixersReturnsEmptyArrayByDefault()
    {
        $this->makeTest(array(), $this->resolver->getFixers());
    }

    public function testResolveFixersWithLevelConfig()
    {
        $this->config->level(FixerInterface::PSR1_LEVEL);

        $this->resolver->resolve();

        $this->makeTest(
            array_merge($this->fixersMap[FixerInterface::PSR0_LEVEL], $this->fixersMap[FixerInterface::PSR1_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithPositiveFixersConfig()
    {
        $this->config->level(FixerInterface::SYMFONY_LEVEL);
        $this->config->fixers(array('strict', 'strict_param'));

        $this->resolver->resolve();

        $expectedFixers = array_merge(
            $this->fixersMap[FixerInterface::PSR0_LEVEL],
            $this->fixersMap[FixerInterface::PSR1_LEVEL],
            $this->fixersMap[FixerInterface::PSR2_LEVEL],
            $this->fixersMap[FixerInterface::SYMFONY_LEVEL],
            array($this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict'], $this->fixersMap[FixerInterface::CONTRIB_LEVEL]['strict_param'])
        );

        $this->makeTest(
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

        $this->makeTest(
            $expectedFixers,
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelOption()
    {
        $this->resolver
            ->setOption('level', 'psr1')
            ->resolve();

        $this->makeTest(
            array_merge($this->fixersMap[FixerInterface::PSR0_LEVEL], $this->fixersMap[FixerInterface::PSR1_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndLevelOption()
    {
        $this->config
            ->level(FixerInterface::PSR2_LEVEL)
            ->fixers(array('strict'));
        $this->resolver
            ->setOption('level', 'psr1')
            ->resolve();

        $this->makeTest(
            array_merge($this->fixersMap[FixerInterface::PSR0_LEVEL], $this->fixersMap[FixerInterface::PSR1_LEVEL]),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndPositiveFixersOption()
    {
        $this->config
            ->level(FixerInterface::PSR2_LEVEL)
            ->fixers(array('strict'));
        $this->resolver
            ->setOption('fixers', 'psr0')
            ->resolve();

        $this->makeTest(
            array($this->fixersMap[FixerInterface::PSR0_LEVEL]['psr0']),
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndNegativeFixersOption()
    {
        $this->config
            ->level(FixerInterface::SYMFONY_LEVEL)
            ->fixers(array('strict'));
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

        $this->makeTest(
            $expectedFixers,
            $this->resolver->getFixers()
        );
    }

    public function testResolveFixersWithLevelConfigAndFixersConfigAndLevelOptionsAndFixersOption()
    {
        $this->config
            ->level(FixerInterface::PSR2_LEVEL)
            ->fixers(array('concat_with_spaces'));
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

        $this->makeTest(
            $expectedFixers,
            $this->resolver->getFixers()
        );
    }
}
