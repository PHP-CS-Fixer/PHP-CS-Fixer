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

namespace PhpCsFixer;

use PhpCsFixer\Console\Application;

/**
 * Obtain information about using version of tool.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ToolInfo
{
    const COMPOSER_PACKAGE_NAME = 'friendsofphp/php-cs-fixer';

    public static function getComposerVersion()
    {
        static $result;

        if (!self::isInstalledByComposer()) {
            throw new \LogicException('Cannot get composer version for tool not installed by composer.');
        }

        if (null === $result) {
            $composerInstalled = json_decode(file_get_contents(self::getComposerInstalledFile()), true);

            foreach ($composerInstalled as $package) {
                if (self::COMPOSER_PACKAGE_NAME === $package['name']) {
                    $result = $package['version'].'#'.$package['dist']['reference'];

                    break;
                }
            }
        }

        return $result;
    }

    public static function getVersion()
    {
        if (self::isInstalledByComposer()) {
            return Application::VERSION.':'.self::getComposerVersion();
        }

        return Application::VERSION;
    }

    public static function isInstalledAsPhar()
    {
        static $result;

        if (null === $result) {
            $result = 'phar://' === substr(__DIR__, 0, 7);
        }

        return $result;
    }

    public static function isInstalledByComposer()
    {
        static $result;

        if (null === $result) {
            $result = !self::isInstalledAsPhar() && file_exists(self::getComposerInstalledFile());
        }

        return $result;
    }

    private static function getComposerInstalledFile()
    {
        return __DIR__.'/../../../composer/installed.json';
    }
}
