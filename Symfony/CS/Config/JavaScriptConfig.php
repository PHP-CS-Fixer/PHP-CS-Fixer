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
use Symfony\CS\Finder\JavaScriptFinder;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class JavaScriptConfig extends Config
{
    public function __construct()
    {
        parent::__construct();

        $this->finder = new JavaScriptFinder();
    }

    public function getName()
    {
        return 'javascript';
    }

    public function getDescription()
    {
        return 'The configuration for a JavaScript pieces in your application';
    }
}
