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
 * Class supports caching information about state of fixing files.
 *
 * Cache is supported only for phar version and version installed via composer.
 *
 * File will be processed by PHP CS Fixer only if any of the following conditions is fulfilled:
 *  - cache is not available,
 *  - fixer version changed,
 *  - file is new,
 *  - file changed.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class FileCacheManager
{
    const CACHE_FILE = '.php_cs.cache';
    const COMPOSER_JSON_FILE = '/../../../composer.json';
    const COMPOSER_LOCK_FILE = '/../../../composer.lock';
    const COMPOSER_PACKAGE_NAME = 'fabpot/php-cs-fixer';

    private $dir;
    private $isEnabled;
    private $newHashes = array();
    private $oldHashes = array();
    private $scriptDir;

    public function __construct($isEnabled, $dir)
    {
        $this->isEnabled = $isEnabled;
        $this->dir = null !== $dir ? $dir.DIRECTORY_SEPARATOR : '';

        $script = $_SERVER['SCRIPT_NAME'];

        if (is_link($script)) {
            $script = dirname($script).'/'.readlink($script);
        }

        $this->scriptDir = dirname($script);

        $this->readFromFile();
    }

    public function __destruct()
    {
        $this->saveToFile();
    }

    public function needFixing($file, $fileContent)
    {
        if (!$this->isCacheAvailable()) {
            return true;
        }

        if (!isset($this->oldHashes[$file])) {
            return true;
        }

        if ($this->oldHashes[$file] !== $this->calcHash($fileContent)) {
            return true;
        }

        // file do not change - keep hash in new collection
        $this->newHashes[$file] = $this->oldHashes[$file];

        return false;
    }

    public function setFile($file, $fileContent)
    {
        if (!$this->isCacheAvailable()) {
            return;
        }

        $this->newHashes[$file] = $this->calcHash($fileContent);
    }

    private function calcHash($content)
    {
        return crc32($content);
    }

    private function getComposerVersion()
    {
        static $result;

        if (!$this->isInstalledByComposer()) {
            throw new \LogicException('Can not get composer version for tool not installed by composer.');
        }

        if (null === $result) {
            $composerLock = json_decode(file_get_contents($this->scriptDir.self::COMPOSER_LOCK_FILE), true);

            foreach ($composerLock['packages'] as $package) {
                if (self::COMPOSER_PACKAGE_NAME === $package['name']) {
                    $result = $package['version'].'#'.$package['dist']['reference'];
                    break;
                }
            }
        }

        return $result;
    }

    private function getVersion()
    {
        if ($this->isInstalledByComposer()) {
            return Fixer::VERSION.':'.$this->getComposerVersion();
        }

        return Fixer::VERSION;
    }

    private function isCacheAvailable()
    {
        static $result;

        if (null === $result) {
            $result = $this->isEnabled && ($this->isInstalledAsPhar() || $this->isInstalledByComposer());
        }

        return $result;
    }

    private function isSameFixerVersion($cacheVersion)
    {
        if (!$this->isCacheAvailable()) {
            return false;
        }

        return $this->getVersion() === $cacheVersion;
    }

    private function isInstalledAsPhar()
    {
        static $result;

        if (null === $result) {
            $result = 'phar://' === substr(__DIR__, 0, 7);
        }

        return $result;
    }

    private function isInstalledByComposer()
    {
        static $result;

        if (null === $result) {
            $result = !$this->isInstalledAsPhar() && file_exists($this->scriptDir.self::COMPOSER_JSON_FILE);
        }

        return $result;
    }

    private function readFromFile()
    {
        if (!$this->isCacheAvailable()) {
            return;
        }

        if (!file_exists($this->dir.self::CACHE_FILE)) {
            return;
        }

        $content = file_get_contents($this->dir.self::CACHE_FILE);
        $data = unserialize($content);

        // Set hashes only if version has not changed.
        // If version changed then we need to parse all files because the fixer changed!
        if ($this->isSameFixerVersion($data['version'])) {
            $this->oldHashes = $data['hashes'];
        }
    }

    private function saveToFile()
    {
        if (!$this->isCacheAvailable()) {
            return;
        }

        $data = serialize(
            array(
                'version' => $this->getVersion(),
                'hashes' => $this->newHashes,
            )
        );

        file_put_contents($this->dir.self::CACHE_FILE, $data, LOCK_EX);
    }
}
