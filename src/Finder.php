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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Finder extends BaseFinder
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->files()
            ->name('*.php')
            ->exclude('vendor')
        ;
    }

    public function getIterator()
    {
        // add config files even if dot files are ignored
        $configFilenameRegex = '\.php_cs(?:\..+)?';
        $this->name('~^'.$configFilenameRegex.'$~is');

        $fx = \Closure::bind(function () { // rebound function can be called without assigment as of PHP 7
            return $this->ignore & static::IGNORE_DOT_FILES;
        }, $this, parent::class);
        $isDotFilesIgnored = $fx();
        if ($isDotFilesIgnored) {
            $this
                ->ignoreDotFiles(false)
                ->notPath('~(?:^|/)(?!'.$configFilenameRegex.'(?:/|$))\..*(?:/|$)~')
            ;
        }

        return parent::getIterator();
    }
}
