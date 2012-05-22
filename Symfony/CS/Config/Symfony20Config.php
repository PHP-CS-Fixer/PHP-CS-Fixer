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

use Symfony\CS\Finder\Symfony20Finder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Symfony20Config extends Config
{
    public function __construct()
    {
        parent::__construct();

        $this->finder = new Symfony20Finder();
    }

    public function getName()
    {
        return 'sf20';
    }

    public function getDescription()
    {
        return 'The configuration for the Symfony 2.0 branch';
    }
}
