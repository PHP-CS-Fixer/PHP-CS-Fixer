<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\CS\FinderInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DefaultFinder extends Finder implements FinderInterface
{
    public function __construct()
    {
        parent::__construct();

        $files = $this->getFilesToExclude();

        $this
            ->files()
            ->name('*.php')
            ->name('*.twig')
            ->name('*.xml')
            ->name('*.yml')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude('vendor')
            ->filter(function (\SplFileInfo $file) use ($files) {
                return !in_array($file->getRelativePathname(), $files, true);
            })
        ;
    }

    public function setDir($dir)
    {
        $this->in($this->getDirs($dir));
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
     * Excludes files because modifying them would break.
     *
     * This is mainly useful for fixtures in unit tests.
     *
     * @return array
     */
    protected function getFilesToExclude()
    {
        return array();
    }
}
