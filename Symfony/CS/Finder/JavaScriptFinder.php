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
 * @author Luis Cordova <cordoval@gmail.com>
 */
class JavaScriptFinder extends Finder implements FinderInterface
{
    public function __construct()
    {
        parent::__construct();

        $files = $this->getFilesToExclude();

        $this
            ->files()
            ->name('*.js')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude('vendor')
            ->filter(function (\SplFileInfo $file) use ($files) {
                return !in_array($file->getRelativePathname(), $files);
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
     * @param $dir
     * @return array
     */
    protected function getDirs($dir)
    {
        return array($dir);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilesToExclude()
    {
        return array();
    }
}
