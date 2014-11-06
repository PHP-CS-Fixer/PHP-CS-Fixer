<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Config;

use Symfony\CS\ConfigInterface;
use Symfony\CS\FinderInterface;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\FixerInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 */
class Config implements ConfigInterface
{
    protected $name;
    protected $description;
    protected $finder;
    protected $level;
    protected $fixers;
    protected $dir;
    protected $customFixers;
    protected $usingCache = false;
    protected $usingLinter = true;

    public function __construct($name = 'default', $description = 'A default configuration')
    {
        $this->name = $name;
        $this->description = $description;
        $this->level = FixerInterface::SYMFONY_LEVEL;
        $this->fixers = array();
        $this->finder = new DefaultFinder();
        $this->customFixers = array();
    }

    public static function create()
    {
        return new static();
    }

    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    public function setUsingCache($usingCache)
    {
        $this->usingCache = $usingCache;

        return $this;
    }

    public function setUsingLinter($usingLinter)
    {
        $this->usingLinter = $usingLinter;

        return $this;
    }

    public function getDir()
    {
        return $this->dir;
    }

    public function finder(\Traversable $finder)
    {
        $this->finder = $finder;

        return $this;
    }

    public function getFinder()
    {
        if ($this->finder instanceof FinderInterface && $this->dir !== null) {
            $this->finder->setDir($this->dir);
        }

        return $this->finder;
    }

    public function level($level)
    {
        $this->level = $level;

        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function fixers($fixers)
    {
        $this->fixers = $fixers;

        return $this;
    }

    public function getFixers()
    {
        return $this->fixers;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function addCustomFixer(FixerInterface $fixer)
    {
        $this->customFixers[] = $fixer;

        return $this;
    }

    public function getCustomFixers()
    {
        return $this->customFixers;
    }

    public function usingCache()
    {
        return $this->usingCache;
    }

    public function usingLinter()
    {
        return $this->usingLinter;
    }
}
