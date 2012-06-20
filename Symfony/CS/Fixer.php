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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Fixer
{
    const VERSION = '0.2';

    protected $fixers = array();
    protected $configs = array();

    public function registerBuiltInFixers()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/Fixer') as $file) {
            $class = 'Symfony\\CS\\Fixer\\'.basename($file, '.php');
            $this->addFixer(new $class());
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
            $class = 'Symfony\\CS\\Config\\'.basename($file, '.php');
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
     */
    public function fix(ConfigInterface $config, $dryRun = false)
    {
        $this->sortFixers();

        $changed = array();
        foreach ($config->getFinder() as $file) {
            if ($file->isDir()) {
                continue;
            }

            if ($appliedFixers = $this->fixFile($file, $config->getFixers(), $dryRun)) {
                if ($file instanceof FinderSplFileInfo) {
                    $changed[$file->getRelativePathname()] = $appliedFixers;
                } else {
                    $changed[$file->getPathname()] = $appliedFixers;
                }
            }
        }

        return $changed;
    }

    public function fixFile(\SplFileInfo $file, $fixerConfig, $dryRun)
    {
        $new = $old = file_get_contents($file->getRealpath());
        $appliedFixers = array();

        $fixers = array();
        if (is_array($fixerConfig)) {
            foreach ($this->fixers as $fixer) {
                if (in_array($fixer->getName(), $fixerConfig)) {
                    $fixers[] = $fixer;
                }
            }
        } else {
            foreach ($this->fixers as $fixer) {
                if ($fixer->getLevel() === ($fixer->getLevel() & $fixerConfig)) {
                    $fixers[] = $fixer;
                }
            }
        }

        foreach ($fixers as $fixer) {
            if (!$fixer->supports($file)) {
                continue;
            }

            $new1 = $fixer->fix($file, $new);
            if ($new1 != $new) {
                $appliedFixers[] = $fixer->getName();
            }
            $new = $new1;
        }

        if ($new != $old) {
            if (!$dryRun) {
                file_put_contents($file->getRealpath(), $new);
            }

            return $appliedFixers;
        }
    }

    public static function getLevelAsString(FixerInterface $fixer)
    {
        if ($fixer->getLevel() === ($fixer->getLevel() & FixerInterface::PSR0_LEVEL)) {
            return 'PSR-0';
        }

        if ($fixer->getLevel() === ($fixer->getLevel() & FixerInterface::PSR1_LEVEL)) {
            return 'PSR-1';
        }

        if ($fixer->getLevel() === ($fixer->getLevel() & FixerInterface::PSR2_LEVEL)) {
            return 'PSR-2';
        }

        return 'all';
    }

    private function sortFixers()
    {
        usort($this->fixers, function ($a, $b) {
            if ($a->getPriority() == $b->getPriority()) {
                return 0;
            }

            return $a->getPriority() > $b->getPriority() ? -1 : 1;
        });
    }
}
