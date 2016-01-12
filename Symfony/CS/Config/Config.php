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

use Symfony\CS\Config as BaseConfig;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 *
 * @deprecated
 */
class Config extends BaseConfig
{
    public function __construct()
    {
        @trigger_error(
            sprintf(
                'The "%s" class is deprecated. You should stop using it, as it will soon be removed in 2.0 version. Use "%s" instead.',
                __CLASS__,
                'Symfony\CS\Config'
            ),
            E_USER_DEPRECATED
        );

        parent::__construct();
    }
}
