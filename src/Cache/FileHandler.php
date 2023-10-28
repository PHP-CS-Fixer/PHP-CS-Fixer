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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
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
        // FRS
        $handle = fopen($this->file, 'r');
        if (false === $handle) {
            return null;
        }
        // FRS
        $cache = $this->readFromHandle($handle);
        // FRS
        $this->fileMTime = $this->getFileCurrentMTime();
        // FRS
        fclose($handle);

        return $cache;
    }

    public function write(CacheInterface $cache): void
    {
        $this->ensureFileIsWriteable();
        // FRS
        $handle = fopen($this->file, 'r+');
        if (false === $handle) {
            return;
        }
        // FRS
        if (method_exists($cache, 'backfillHashes') && $this->fileMTime < $this->getFileCurrentMTime()) {
            // FRS
            flock($handle, LOCK_EX);
            // FRS
            var_dump('backfill??');
            $oldCache = $this->readFromHandle($handle);
            // FRS
            rewind($handle);

            if (null !== $oldCache) {
                $cache->backfillHashes($oldCache);
            }
        }
        // FRS
        ftruncate($handle, 0);
        // FRS
        var_dump(getmypid().' FRS WRITE');
        fwrite($handle, $cache->toJson());
        // FRS
        fflush($handle);
        // FRS
        fsync($handle);
        $this->fileMTime = time(); // we could take the fresh `mtime` of file that we just modified with `$this->getFileCurrentMTime()`, but `time()` should be good enough here and reduce IO operation
        // FRS
        fclose($handle);
    }

    private function getFileCurrentMTime(): int
    {
        // FRS
        clearstatcache(true, $this->file);
        // FRS
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
            // FRS
            $size = @filesize($this->file);
            if (false === $size || 0 === $size) {
                return null;
            }
            // FRS
            var_dump(getmypid().' FRS READ');
            $content = fread($handle, $size);

            return Cache::fromJson($content);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    private function ensureFileIsWriteable(): void
    {
        // FRS
        if (file_exists($this->file)) {
            // FRS
            if (is_dir($this->file)) {
                throw new IOException(
                    sprintf('Cannot write cache file "%s" as the location exists as directory.', realpath($this->file)),
                    0,
                    null,
                    $this->file
                );
            }
            // FRS
            if (!is_writable($this->file)) {
                throw new IOException(
                    sprintf('Cannot write to file "%s" as it is not writable.', realpath($this->file)),
                    0,
                    null,
                    $this->file
                );
            }
        } else {
            // FRS
            $dir = \dirname($this->file);

            // Ensure path is created, but ignore if already exists. FYI: ignore EA suggestion in IDE,
            // `mkdir()` returns `false` for existing paths, so we can't mix it with `is_dir()` in one condition.
            // FRS
            if (!is_dir($dir)) {
                // FRS
                @mkdir($dir, 0777, true);
            }
            // FRS
            if (!is_dir($dir)) {
                throw new IOException(
                    sprintf('Directory of cache file "%s" does not exists and couldn\'t be created.', $this->file),
                    0,
                    null,
                    $this->file
                );
            }
            // FRS
            @touch($this->file);
            // FRS
            @chmod($this->file, 0666);
        }
    }
}
