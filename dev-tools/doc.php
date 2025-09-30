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


namespace PhpCsFixer;

use Composer\XdebugHandler\XdebugHandler;
use PhpCsFixer\Console\Command\DocumentationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

error_reporting(\E_ALL & ~\E_DEPRECATED & ~\E_USER_DEPRECATED);

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (0 !== ($severity & error_reporting())) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    return true;
});

// load dependencies
(static function (): void {
    require __DIR__.'/../vendor/autoload.php';
})();

// Restart if xdebug is loaded, unless the environment variable PHP_CS_FIXER_ALLOW_XDEBUG is set.
$xdebug = new XdebugHandler('PHP_CS_FIXER');
$xdebug->check();
unset($xdebug);

$command = new DocumentationCommand(new Filesystem());

$application = new Application();
$application->addCommands([$command]);
$application
    ->setDefaultCommand($command->getName(), true)
    ->run()
;
