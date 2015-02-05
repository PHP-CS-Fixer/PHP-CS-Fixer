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

use Symfony\CS\Finder\Symfony23Finder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Symfony23Config extends Config
{
    protected $header = <<< EOH
This file is part of the Symfony package.

(c) Fabien Potencier <fabien@symfony.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOH;

    public function __construct()
    {
        parent::__construct();

        $this->finder = new Symfony23Finder();
    }

    public function getName()
    {
        return 'sf23';
    }

    public function getDescription()
    {
        return 'The configuration for the Symfony 2.3+ branch';
    }
}
