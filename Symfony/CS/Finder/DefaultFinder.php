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

namespace Symfony\CS\Finder;

use Symfony\CS\Finder as BaseFinder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated
 */
class DefaultFinder extends BaseFinder
{
    public function __construct()
    {
        @trigger_error(
            sprintf(
                'The "%s" class is deprecated. You should stop using it, as it will soon be removed in 2.0 version. Use "%s" instead.',
                __CLASS__,
                'Symfony\CS\Finder'
            ),
            E_USER_DEPRECATED
        );

        parent::__construct();

        $files = $this->getFilesToExclude();

        $this
            ->name('*.xml')
            ->name('*.yml')
            ->filter(
                function (\SplFileInfo $file) use ($files) {
                    return !in_array($file->getRelativePathname(), $files, true);
                }
            )
        ;
    }

    public function setDir($dir)
    {
        @trigger_error(
            sprintf(
                'The "%s" method is deprecated. You should stop using it, as it will soon be removed in 2.0 version. Use "%s" instead.',
                __METHOD__,
                'in'
            ),
            E_USER_DEPRECATED
        );

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
