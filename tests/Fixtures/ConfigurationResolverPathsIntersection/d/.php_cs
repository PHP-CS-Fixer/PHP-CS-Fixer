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

if (!class_exists('ConfigurationResolverPathsIntersection_d_Config')) {
    class ConfigurationResolverPathsIntersection_d_Config extends PhpCsFixer\Config
    {
    }
}

return ConfigurationResolverPathsIntersection_d_Config::create()
    ->finder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.'/..')
    )
;
