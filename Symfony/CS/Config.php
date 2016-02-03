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

namespace Symfony\CS;

use Symfony\CS\Finder\DefaultFinder;

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
    protected $hideProgress = false;

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

    public function getHideProgress()
    {
        return $this->hideProgress;
    }

    public function addCustomFixer(FixerInterface $fixer)
    {
        $this->customFixers[] = $fixer;

        return $this;
    }

    public function addCustomFixers($fixers)
    {
        if (false === is_array($fixers) && false === $fixers instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Argument must be an array or a Traversable, got "%s".',
                is_object($fixers) ? get_class($fixers) : gettype($fixers)
            ));
        }

        foreach ($fixers as $fixer) {
            $this->addCustomFixer($fixer);
        }

        return $this;
    }

    public function getCustomFixers()
    {
        return $this->customFixers;
    }

    public function hideProgress($hideProgress)
    {
        $this->hideProgress = $hideProgress;

        return $this;
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
