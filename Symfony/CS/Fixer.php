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

    public function fix(\Traversable $iterator, $dryRun = false)
    {
        $this->sortFixers();

        $changed = array();
        foreach ($iterator as $file) {
            if ($this->fixFile($file, $dryRun)) {
                $changed[] = $file->getRelativePathname();
            }
        }

        return $changed;
    }

    public function fixFile(\SplFileInfo $file, $dryRun = false)
    {
        $new = $old = file_get_contents($file->getRealpath());

        foreach ($this->fixers as $fixer) {
            if ($fixer->supports($file)) {
                $new = $fixer->fix($file, $new);
            }
        }

        if ($new != $old) {
            if (!$dryRun) {
                file_put_contents($file->getRealpath(), $new);
            }

            return true;
        }
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
