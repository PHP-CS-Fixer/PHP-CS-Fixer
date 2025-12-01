<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$config = require __DIR__.'/../.php-cs-fixer.dist.php';

$config->getFinder()
    ->ignoreVCSIgnored(false)
    ->in(__DIR__.'/../vendor/')
;

return $config->setRules([
    'short_scalar_cast' => true,
]);
