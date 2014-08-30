<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Config;

use Symfony\CS\Finder\MagentoFinder;

/**
 * @author Myke Hines <myke@webhines.com>
 */
class MagentoConfig extends Config
{
    public function __construct()
    {
        parent::__construct();

        $this->finder = new MagentoFinder();
    }

    public function getName()
    {
        return 'magento';
    }

    public function getDescription()
    {
        return 'The configuration for a Magento application';
    }
}
