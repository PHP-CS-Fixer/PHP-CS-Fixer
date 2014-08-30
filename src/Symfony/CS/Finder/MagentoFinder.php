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
 * @author Myke Hines <myke@webhines.com>
 */
class MagentoFinder extends DefaultFinder
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->name('*.php')
            ->name('*.phtml')
            ->name('*.xml')
            ->exclude(array(
                'lib',
                'shell',
                'app/Mage.php',
                'app/code/core',
                'app/code/community',
                'app/design/frontend/default',
                'app/design/frontend/enterprise/default',
                'app/design/frontend/base',
                'app/design/adminhtml/default')
            )
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
     * Excludes files because modifying them would break (mainly useful for fixtures in unit tests).
     *
     * @return array
     */
    protected function getFilesToExclude()
    {
        return array();
    }
}
