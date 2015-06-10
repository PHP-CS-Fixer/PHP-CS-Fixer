<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\CS\Config\Config;

/**
 * The class that returns invalid data for a test.
 */
class TestConfigCase6 extends Config
{
    public function getFixerOutput()
    {
        return $this;
    }
}
