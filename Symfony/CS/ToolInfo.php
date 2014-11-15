<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * Obtain information about using version of tool.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ToolInfo
{
    const COMPOSER_JSON_FILE = '/../../../composer.json';
    const COMPOSER_LOCK_FILE = '/../../../composer.lock';
    const COMPOSER_PACKAGE_NAME = 'fabpot/php-cs-fixer';

    public static function getComposerVersion()
    {
        static $result;

        if (!self::isInstalledByComposer()) {
            throw new \LogicException('Can not get composer version for tool not installed by composer.');
        }

        if (null === $result) {
            $composerLock = json_decode(file_get_contents(self::getScriptDir().self::COMPOSER_LOCK_FILE), true);

            foreach ($composerLock['packages'] as $package) {
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
                $script = dirname($script).'/'.readlink($script);
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
            $result = !self::isInstalledAsPhar() && file_exists(self::getScriptDir().self::COMPOSER_JSON_FILE);
        }

        return $result;
    }
}
