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

        $content = file_get_contents($this->file);

        try {
            $cache = Cache::fromJson($content);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }

        return $cache;
    }

    public function write(CacheInterface $cache): void
    {
        $content = $cache->toJson();

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

        $bytesWritten = @file_put_contents($this->file, $content);

        if (false === $bytesWritten) {
            $error = error_get_last();

            throw new IOException(
                sprintf('Failed to write file "%s", "%s".', $this->file, $error['message'] ?? 'no reason available'),
                0,
                null,
                $this->file
            );
        }
    }
}
