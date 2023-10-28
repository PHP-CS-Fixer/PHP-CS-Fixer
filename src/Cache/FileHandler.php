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

namespace PhpCsFixer\Cache;

use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class FileHandler implements FileHandlerInterface
{
    private string $file;

    private int $fileMTime = 0;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function read(): ?CacheInterface
    {
        if (!file_exists($this->file)) {
            return null;
        }

        $handle = fopen($this->file, 'r');
        if (false === $handle) {
            return null;
        }

        $cache = $this->readFromHandle($handle);
        $this->fileMTime = $this->getFileCurrentMTime();

        fclose($handle);

        return $cache;
    }

    public function write(CacheInterface $cache): void
    {
        $this->ensureFileIsWriteable();

        $handle = fopen($this->file, 'r+');
        if (false === $handle) {
            return;
        }

        if (method_exists($cache, 'backfillHashes') && $this->fileMTime < $this->getFileCurrentMTime()) {
            flock($handle, LOCK_EX);
            $oldCache = $this->readFromHandle($handle);
            rewind($handle);

            if (null !== $oldCache) {
                $cache->backfillHashes($oldCache);
            }
        }
        ftruncate($handle, 0);
        fwrite($handle, $cache->toJson());
        fflush($handle);
        fsync($handle);
        $this->fileMTime = time(); // we could take the fresh `mtime` of file that we just modified with `$this->getFileCurrentMTime()`, but `time()` should be good enough here and reduce IO operation
        fclose($handle);
    }

    private function getFileCurrentMTime(): int
    {
        clearstatcache(true, $this->file);

        $mtime = filemtime($this->file);

        if (false === $mtime) {
            // cannot check mtime? OK, let's pretend file is old
            $mtime = 0;
        }

        return $mtime;
    }

    /**
     * @param resource $handle
     */
    private function readFromHandle($handle): ?CacheInterface
    {
        try {
            $size = @filesize($this->file);
            if (false === $size || 0 === $size) {
                return null;
            }

            $content = fread($handle, $size);

            return Cache::fromJson($content);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    private function ensureFileIsWriteable(): void
    {
        if (file_exists($this->file)) {
            if (is_dir($this->file)) {
                throw new IOException(
                    sprintf('Cannot write cache file "%s" as the location exists as directory.', realpath($this->file)),
                    0,
                    null,
                    $this->file
                );
            }

            if (!is_writable($this->file)) {
                throw new IOException(
                    sprintf('Cannot write to file "%s" as it is not writable.', realpath($this->file)),
                    0,
                    null,
                    $this->file
                );
            }
        } else {
            $dir = \dirname($this->file);

            // Ensure path is created, but ignore if already exists. FYI: ignore EA suggestion in IDE,
            // `mkdir()` returns `false` for existing paths, so we can't mix it with `is_dir()` in one condition.
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            if (!is_dir($dir)) {
                throw new IOException(
                    sprintf('Directory of cache file "%s" does not exists and couldn\'t be created.', $this->file),
                    0,
                    null,
                    $this->file
                );
            }

            @touch($this->file);
            @chmod($this->file, 0666);
        }
    }
}
