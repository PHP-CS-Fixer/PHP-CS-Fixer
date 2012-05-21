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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Fixer
{
    const VERSION = '0.1';

    protected $fixers = array();

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

    /**
     * Fixes all files in the given iterator.
     *
     * @param \Traversable  $iterator    A file iterator
     * @param array|integer $fixerConfig A level or a list of fixer names
     * @param Boolean       $dryRun      Whether to simulate the changes or not
     */
    public function fix(\Traversable $iterator, $fixerConfig = FixerInterface::ALL_LEVEL, $dryRun = false)
    {
        $this->sortFixers();

        $changed = array();
        foreach ($iterator as $file) {
            if ($this->fixFile($file, $fixerConfig, $dryRun)) {
                $changed[] = $file->getRelativePathname();
            }
        }

        return $changed;
    }

    public function fixFile(\SplFileInfo $file, $fixerConfig = FixerInterface::ALL_LEVEL, $dryRun = false)
    {
        $new = $old = file_get_contents($file->getRealpath());

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

            $new = $fixer->fix($file, $new);
        }

        if ($new != $old) {
            if (!$dryRun) {
                file_put_contents($file->getRealpath(), $new);
            }

            return true;
        }
    }

    public function getLevelAsString(FixerInterface $fixer)
    {
        if ($fixer->getLevel() !== ($fixer->getLevel() & FixerInterface::PSR1_LEVEL)) {
            return 'PSR-1';
        }

        if ($fixer->getLevel() !== ($fixer->getLevel() & FixerInterface::PSR2_LEVEL)) {
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
