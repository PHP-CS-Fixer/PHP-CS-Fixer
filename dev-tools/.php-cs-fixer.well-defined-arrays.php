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

$config->setRules([
    'phpdoc_array_type' => true,
    'phpdoc_list_type' => true,
]);

return $config;
