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

namespace PhpCsFixer;

use Symfony\Component\Finder\Finder as BaseFinder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiñski <dariusz.ruminski@gmail.com>
 */
class Finder extends BaseFinder implements FinderInterface
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->files()
            ->name('*.php')
            ->name('*.phpt')
            ->name('*.twig')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude('vendor')
        ;

        $files = $this->getFilesToExclude();

        if (!empty($files)) {
            $this->filter(
                function (\SplFileInfo $file) use ($files) {
                    return !in_array($file->getRelativePathname(), $files, true);
                }
            );
        }
    }

    public function setDir($dir)
    {
        $this->in($this->getDirs($dir));
    }

    /**
     * Gets the directories that needs to be scanned for files to validate.
     *
     * @param string $dir
     *
     * @return string[]
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
     * @return string[]
     */
    protected function getFilesToExclude()
    {
        return array();
    }
}
