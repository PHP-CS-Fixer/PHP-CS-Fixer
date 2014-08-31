<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use Symfony\Component\Stopwatch\Stopwatch;
use SebastianBergmann\Diff\Differ;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Fixer
{
    const VERSION = '1.0-DEV';

    protected $fixers = array();
    protected $configs = array();
    protected $diff;

    /**
     * Stopwatch instance.
     *
     * @var \Symfony\Component\Stopwatch\Stopwatch|null
     */
    protected $stopwatch;

    public function __construct()
    {
        $this->diff = new Differ();
    }

    public static function cmpInt($a, $b)
    {
        if ($a === $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }

    public function registerBuiltInFixers()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/Fixer') as $file) {
            $relativeNamespace = $file->getRelativePath();
            $class = 'Symfony\\CS\\Fixer\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
            $this->addFixer(new $class());
        }
    }

    public function registerCustomFixers($fixers)
    {
        foreach ($fixers as $fixer) {
            $this->addFixer($fixer);
        }
    }

    public function addFixer(FixerInterface $fixer)
    {
        $this->fixers[] = $fixer;
    }

    public function getFixers()
    {
        $this->sortFixers();

        return $this->fixers;
    }

    public function registerBuiltInConfigs()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/Config') as $file) {
            $relativeNamespace = $file->getRelativePath();
            $class = 'Symfony\\CS\\Config\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
            $this->addConfig(new $class());
        }
    }

    public function addConfig(ConfigInterface $config)
    {
        $this->configs[] = $config;
    }

    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Fixes all files for the given finder.
     *
     * @param ConfigInterface $config A ConfigInterface instance
     * @param Boolean         $dryRun Whether to simulate the changes or not
     * @param Boolean         $diff   Whether to provide diff
     *
     * @return array
     */
    public function fix(ConfigInterface $config, $dryRun = false, $diff = false)
    {
        $fixers = $this->prepareFixers($config);
        $changed = array();

        if ($this->stopwatch) {
            $this->stopwatch->openSection();
        }

        foreach ($config->getFinder() as $file) {
            if ($file->isDir()) {
                continue;
            }

            if ($fixInfo = $this->fixFile($file, $fixers, $dryRun, $diff)) {
                $changed[$this->getFileRelativePathname($file)] = $fixInfo;
            }
        }

        if ($this->stopwatch) {
            $this->stopwatch->stopSection('fixFile');
        }

        return $changed;
    }

    public function fixFile(\SplFileInfo $file, array $fixers, $dryRun, $diff)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start($this->getFileRelativePathname($file));
        }

        $new = $old = file_get_contents($file->getRealpath());
        $appliedFixers = array();

        // we do not need Tokens to still caching previously fixed file - so clear the cache
        Tokens::clearCache();

        foreach ($fixers as $fixer) {
            if (!$fixer->supports($file)) {
                continue;
            }

            $newest = $fixer->fix($file, $new);
            if ($newest !== $new) {
                $appliedFixers[] = $fixer->getName();
            }
            $new = $newest;
        }

        $fixInfo = null;

        if ($new !== $old) {
            if (!$dryRun) {
                file_put_contents($file->getRealpath(), $new);
            }

            $fixInfo = array('appliedFixers' => $appliedFixers);

            if ($diff) {
                $fixInfo['diff'] = $this->stringDiff($old, $new);
            }
        }

        if ($this->stopwatch) {
            $this->stopwatch->stop($this->getFileRelativePathname($file));
        }

        return $fixInfo;
    }

    private function getFileRelativePathname(\SplFileInfo $file)
    {
        if ($file instanceof FinderSplFileInfo) {
            return $file->getRelativePathname();
        }

        return $file->getPathname();
    }

    public static function getLevelAsString(FixerInterface $fixer)
    {
        $level = $fixer->getLevel();

        if ($level === ($level & FixerInterface::PSR0_LEVEL)) {
            return 'PSR-0';
        }

        if ($level === ($level & FixerInterface::PSR1_LEVEL)) {
            return 'PSR-1';
        }

        if ($level === ($level & FixerInterface::PSR2_LEVEL)) {
            return 'PSR-2';
        }

        if ($level === ($level & FixerInterface::CONTRIB_LEVEL)) {
            return 'contrib';
        }

        return 'all';
    }

    protected function stringDiff($old, $new)
    {
        $diff = $this->diff->diff($old, $new);

        $diff = implode(PHP_EOL, array_map(function ($string) {
            $string = preg_replace('/^(\+){3}/', '<info>+++</info>', $string);
            $string = preg_replace('/^(\+){1}/', '<info>+</info>', $string);

            $string = preg_replace('/^(\-){3}/', '<error>---</error>', $string);
            $string = preg_replace('/^(\-){1}/', '<error>-</error>', $string);

            $string = str_repeat(' ', 6).$string;

            return $string;
        }, explode(PHP_EOL, $diff)));

        return $diff;
    }

    private function sortFixers()
    {
        $selfName = __CLASS__;

        usort($this->fixers, function (FixerInterface $a, FixerInterface $b) use ($selfName) {
            return $selfName::cmpInt($b->getPriority(), $a->getPriority());
        });
    }

    private function prepareFixers(ConfigInterface $config)
    {
        $fixers = $config->getFixers();

        foreach ($fixers as $fixer) {
            if ($fixer instanceof ConfigAwareInterface) {
                $fixer->setConfig($config);
            }
        }

        return $fixers;
    }

    public function setStopwatch(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }
}
