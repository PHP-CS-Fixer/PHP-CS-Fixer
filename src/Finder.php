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
use Symfony\Component\Finder\Iterator\LazyIterator;

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

        // add config files even if dot files are ignored
        if (class_exists(LazyIterator::class)) { // LazyIterator class is available since Symfony 4.4 (which requires PHP 7+)
            $this->append(new \IteratorIterator(new LazyIterator(function () {
                $iterator = clone $this;
                $iterator->ignoreDotFiles(false);
                $iterator->name('~^\.php_cs(?:\..+)?$~is');

                return $iterator;
            })));
        }
    }
}
