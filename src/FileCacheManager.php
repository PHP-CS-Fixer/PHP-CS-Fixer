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

use PhpCsFixer\Cache\Cache;
use PhpCsFixer\Cache\CacheInterface;
use PhpCsFixer\Cache\HandlerInterface;
use PhpCsFixer\Cache\SignatureInterface;

/**
 * Class supports caching information about state of fixing files.
 *
 * Cache is supported only for phar version and version installed via composer.
 *
 * File will be processed by PHP CS Fixer only if any of the following conditions is fulfilled:
 *  - cache is not available,
 *  - fixer version changed,
 *  - rules changed,
 *  - file is new,
 *  - file changed.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileCacheManager
{
    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @var string
     */
    private $cacheFileRealDirName;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var bool
     */
    private $isDryRun;

    /**
     * @param HandlerInterface   $handler
     * @param SignatureInterface $signature
     * @param bool               $isDryRun
     */
    public function __construct(HandlerInterface $handler, SignatureInterface $signature, $isDryRun = false)
    {
        $this->handler = $handler;
        $this->signature = $signature;
        $this->cacheFileRealDirName = dirname(realpath($handler->file()));
        $this->isDryRun = $isDryRun;

        $this->readCache();
    }

    public function __destruct()
    {
        $this->writeCache();
    }

    private function readCache()
    {
        $cache = $this->handler->read();

        if (!$cache || !$this->signature->equals($cache->signature())) {
            $cache = new Cache($this->signature);
        }

        $this->cache = $cache;
    }

    private function writeCache()
    {
        $this->handler->write($this->cache);
    }

    public function needFixing($file, $fileContent)
    {
        $file = $this->getRelativePathname($file);

        if (!$this->cache->has($file) || $this->cache->get($file) !== $this->calcHash($fileContent)) {
            return true;
        }

        return false;
    }

    public function setFile($file, $fileContent)
    {
        $file = $this->getRelativePathname($file);

        $hash = $this->calcHash($fileContent);

        if ($this->isDryRun && $this->cache->has($file) && $this->cache->get($file) !== $hash) {
            $this->cache->clear($file);

            return;
        }

        $this->cache->set($file, $hash);
    }

    private function normalizePath($path)
    {
        return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
    }

    private function getRelativePathname($file)
    {
        $file = $this->normalizePath($file);

        if (0 !== stripos($file, $this->cacheFileRealDirName.DIRECTORY_SEPARATOR)) {
            return $file;
        }

        return substr($file, strlen($this->cacheFileRealDirName) + 1);
    }

    private function calcHash($content)
    {
        return crc32($content);
    }
}
