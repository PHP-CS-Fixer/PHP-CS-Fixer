#!/usr/bin/env php
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

require_once __DIR__.'/../vendor/autoload.php';

$version = [
    'number' => PhpCsFixer\Console\Application::VERSION,
    'vnumber' => 'v'.PhpCsFixer\Console\Application::VERSION,
    'codename' => PhpCsFixer\Console\Application::VERSION_CODENAME,
];

echo json_encode([
    'version' => $version,
], JSON_PRETTY_PRINT);
