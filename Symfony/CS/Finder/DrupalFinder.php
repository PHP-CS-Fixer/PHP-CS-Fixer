<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * Myke Hines <myke@webhines.com>
 *
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Finder;

use Symfony\Component\Finder\Finder;

/**
 * @author Peter Drake <pdrake@gmail.com>
 */
class DrupalFinder extends DefaultFinder

{
    public function __construct()
    {
        parent::__construct();

        $this
            ->files()
            ->name('*.php')
            ->name('*.module')
            ->name('*.inc')
            ->name('*.install')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude('libraries')
        ;
    }
}
