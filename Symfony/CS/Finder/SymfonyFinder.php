<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Finder;

use Symfony\Component\Finder\Finder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SymfonyFinder extends Finder
{
    public function __construct($dir)
    {
        parent::__construct();

        $files = $this->getFileToExclude();

        $this
            ->files()
            ->name('*.md')
            ->name('*.php')
            ->name('*.twig')
            ->name('*.xml')
            ->name('*.yml')
            ->exclude('.git')
            ->exclude('vendor')
            ->filter(function (\SplFileInfo $file) use ($files) {
                return !in_array($file->getRelativePathname(), $files);
            })
            ->in($this->getDirs($dir))
        ;
    }

    /**
     * Gets the directories that needs to be scanned for files to validate.
     *
     * @return array
     */
    protected function getDirs($dir)
    {
        return array($dir);
    }

    /**
     * Excludes files because modifying them would break (mainly useful for fixtures in unit tests).
     *
     * @return array
     */
    protected function getFileToExclude()
    {
        return array();
    }
}
