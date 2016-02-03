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

/**
 * @author Myke Hines <myke@webhines.com>
 *
 * @deprecated
 */
class MagentoFinder extends DefaultFinder
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

        $this
            ->name('*.php')
            ->name('*.phtml')
            ->name('*.xml')
            ->exclude(
                array(
                    'lib',
                    'shell',
                    'app/Mage.php',
                    'app/code/core',
                    'app/code/community',
                    'app/design/frontend/default',
                    'app/design/frontend/enterprise/default',
                    'app/design/frontend/base',
                    'app/design/adminhtml/default',
                )
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
     * @param string $dir
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
