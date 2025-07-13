#!/usr/bin/env php
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

error_reporting(\E_ALL & ~\E_DEPRECATED & ~\E_USER_DEPRECATED);

require __DIR__.'/../vendor/autoload.php';

use PhpCsFixer\Console\Command\DocumentationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

$command = new DocumentationCommand(new Filesystem());

$application = new Application();
$application->add($command);
$application
    ->setDefaultCommand($command->getName(), true)
    ->run()
;
