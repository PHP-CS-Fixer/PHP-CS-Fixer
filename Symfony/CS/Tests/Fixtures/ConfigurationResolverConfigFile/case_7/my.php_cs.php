<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\CS\ConfigInterface;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\FixerInterface;

/**
 * Test configuration class for a PHPUnit test.
 */
class CustomConfig implements ConfigInterface
{
    private $fixers = array();

    public function getName()
    {
        return 'TestCase7';
    }

    public function getDescription()
    {
        return 'Test config for PHPUnit test case 7';
    }

    public function getFinder()
    {
        return new DefaultFinder();
    }

    public function getLevel()
    {
        return FixerInterface::NONE_LEVEL;
    }

    public function getFixers()
    {
        return $this->fixers;
    }

    public function setDir($dir)
    {
        return $this;
    }

    public function getDir()
    {
        return __DIR__;
    }

    public function getHideProgress()
    {
        return true;
    }

    public function addCustomFixer(\Symfony\CS\FixerInterface $fixer)
    {
        return $this;
    }

    public function getCustomFixers()
    {
        return array();
    }

    public function usingCache()
    {
        return false;
    }

    public function usingLinter()
    {
        return false;
    }

    public function setCacheFile($cacheFile)
    {
        return $this;
    }

    public function getCacheFile()
    {
        return __DIR__.'/test.cache';
    }

    public function getPhpExecutable()
    {
        return;
    }

    public function fixers(array $fixers)
    {
        $this->fixers = $fixers;

        return $this;
    }

    public function setUsingCache($usingCache)
    {
        return false;
    }
}

return new CustomConfig();
