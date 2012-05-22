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

use Symfony\CS\Finder\Symfony21Finder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Symfony21Config extends Config
{
    public function __construct()
    {
        parent::__construct();

        $this->finder = new Symfony21Finder();
    }

    public function getName()
    {
        return 'sf21';
    }

    public function getDescription()
    {
        return 'The configuration for the Symfony 2.1 branch';
    }
}
