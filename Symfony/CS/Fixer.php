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
use SebastianBergmann\Diff\Differ;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Fixer
{
    const VERSION = '0.5-DEV';

    protected $fixers = array();
    protected $configs = array();
    protected $diff;

    public function __construct()
    {
        $this->diff = new Differ();
    }

    public function registerBuiltInFixers()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/Fixer') as $file) {
            $class = 'Symfony\\CS\\Fixer\\'.basename($file, '.php');
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
     * @param Boolean         $diff   Whether to provide diff
     */
    public function fix(ConfigInterface $config, $dryRun = false, $diff = false)
    {
        $this->sortFixers();

        $fixers = $this->prepareFixers($config);
        $changed = array();
        foreach ($config->getFinder() as $file) {
            if ($file->isDir()) {
                continue;
            }

            if ($fixInfo = $this->fixFile($file, $fixers, $dryRun, $diff)) {
                if ($file instanceof FinderSplFileInfo) {
                    $changed[$file->getRelativePathname()] = $fixInfo;
                } else {
                    $changed[$file->getPathname()] = $fixInfo;
                }
            }
        }

        return $changed;
    }

    public function fixFile(\SplFileInfo $file, array $fixers, $dryRun, $diff)
    {
        $new = $old = file_get_contents($file->getRealpath());
        $appliedFixers = array();

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

        if ($new !== $old) {
            if (!$dryRun) {
                file_put_contents($file->getRealpath(), $new);
            }

            $fixInfo = array('appliedFixers' => $appliedFixers);

            if ($diff) {
                $fixInfo['diff'] = $this->stringDiff($old, $new);
            }

            return $fixInfo;
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

    protected function stringDiff($old, $new)
    {
        $diff = $this->diff->diff($old, $new);

        $diff = implode(PHP_EOL, array_map(function ($string) {
            $string = preg_replace('/^(\+){3}/', '<info>+++</info>', $string);
            $string = preg_replace('/^(\+){1}/', '<info>+</info>', $string);

            $string = preg_replace('/^(\-){3}/', '<error>---</error>', $string);
            $string = preg_replace('/^(\-){1}/', '<error>-</error>', $string);

            $string = str_repeat(' ', 6) . $string;

            return $string;
        }, explode(PHP_EOL, $diff)));

        return $diff;
    }

    private function sortFixers()
    {
        usort($this->fixers, function ($a, $b) {
            if ($a->getPriority() === $b->getPriority()) {
                return 0;
            }

            return $a->getPriority() > $b->getPriority() ? -1 : 1;
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
}
