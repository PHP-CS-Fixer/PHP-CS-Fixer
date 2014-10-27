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

class FixersResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $config;
    protected $resolver;

    protected function setUp()
    {
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();
        $fixer->registerBuiltInConfigs();

        $this->config = new Config();

        $this->resolver = new FixersResolver($fixer->getFixers(), $this->config);
    }

    public function testGetFixers()
    {
        $fixers = $this->resolver->resolve('psr1', null);

        $this->assertEquals($fixers, $this->resolver->getFixers());
    }

    public function testGetFixersReturnsEmptyArrayByDefault()
    {
        $this->assertEquals(array(), $this->resolver->getFixers());
    }

    public function testResolveWithOptionLevel()
    {
        $fixers = $this->resolver->resolve('psr1', null);

        foreach ($fixers as $fixer) {
            $this->assertTrue(in_array($fixer->getLevel(), array(FixerInterface::PSR0_LEVEL, FixerInterface::PSR1_LEVEL), true));
        }
    }

    public function testResolveWithConfigLevel()
    {
        $this->config->level(FixerInterface::PSR1_LEVEL);

        $fixers = $this->resolver->resolve(null, null);

        foreach ($fixers as $fixer) {
            $this->assertTrue(in_array($fixer->getLevel(), array(FixerInterface::PSR0_LEVEL, FixerInterface::PSR1_LEVEL), true));
        }
    }

    public function testResolveWithIncludeOptionFixers()
    {
        $fixers = $this->resolver->resolve(null, 'encoding, php_closing_tag');

        foreach ($fixers as $fixer) {
            $this->assertTrue(in_array($fixer->getName(), array('encoding', 'php_closing_tag'), true));
        }
    }

    public function testResolveWithConfigFixers()
    {
        $this->config->fixers(array('encoding', 'php_closing_tag'));

        $fixers = $this->resolver->resolve(null, null);

        foreach ($fixers as $fixer) {
            $this->assertTrue(in_array($fixer->getName(), array('encoding', 'php_closing_tag'), true));
        }
    }

    public function testResolveWithConfigLevelAndExcludeOptionFixers()
    {
        $this->config->level('psr1');

        $fixers = $this->resolver->resolve(null, '-encoding');

        $enabledEncoding = false;

        foreach ($fixers as $fixer) {
            $this->assertTrue(in_array($fixer->getLevel(), array(FixerInterface::PSR0_LEVEL, FixerInterface::PSR1_LEVEL), true));

            if ($fixer->getName() === 'encoding') {
                $this->fail();
            }
        }
    }

    public function testResolveWithOptionLevelAndIncludeAndExcludeOptionFixers()
    {
        $fixers = $this->resolver->resolve('psr1', '-encoding,php_closing_tag');

        $enabledEncoding = false;
        $enabledPhpClosingTag = false;

        foreach ($fixers as $fixer) {
            switch ($fixer->getName()) {
                case 'encoding':        // psr1
                    $enabledEncoding = true;
                    break;
                case 'php_closing_tag': // psr2
                    $enabledPhpClosingTag = true;
                    break;
                default:
                    $this->assertTrue(in_array($fixer->getLevel(), array(FixerInterface::PSR0_LEVEL, FixerInterface::PSR1_LEVEL), true));
                    break;
            }
        }

        $this->assertFalse($enabledEncoding);
        $this->assertTrue($enabledPhpClosingTag);
    }
}
