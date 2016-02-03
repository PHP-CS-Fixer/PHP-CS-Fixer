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

/**
 * Obtain information about using version of tool.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ToolInfo
{
    const COMPOSER_INSTALLED_FILE = '/../../composer/installed.json';
    const COMPOSER_PACKAGE_NAME = 'fabpot/php-cs-fixer';

    public static function getComposerVersion()
    {
        static $result;

        if (!self::isInstalledByComposer()) {
            throw new \LogicException('Can not get composer version for tool not installed by composer.');
        }

        if (null === $result) {
            $composerInstalled = json_decode(file_get_contents(self::getScriptDir().self::COMPOSER_INSTALLED_FILE), true);

            foreach ($composerInstalled as $package) {
                if (self::COMPOSER_PACKAGE_NAME === $package['name']) {
                    $result = $package['version'].'#'.$package['dist']['reference'];
                    break;
                }
            }
        }

        return $result;
    }

    private static function getScriptDir()
    {
        static $result;

        if (null === $result) {
            $script = $_SERVER['SCRIPT_NAME'];

            if (is_link($script)) {
                $linkTarget = readlink($script);

                // If the link target is relative to the link
                if (false === realpath($linkTarget)) {
                    $linkTarget = dirname($script).'/'.$linkTarget;
                }

                $script = $linkTarget;
            }

            $result = dirname($script);
        }

        return $result;
    }

    public static function getVersion()
    {
        if (self::isInstalledByComposer()) {
            return Fixer::VERSION.':'.self::getComposerVersion();
        }

        return Fixer::VERSION;
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
            $result = !self::isInstalledAsPhar() && file_exists(self::getScriptDir().self::COMPOSER_INSTALLED_FILE);
        }

        return $result;
    }
}
