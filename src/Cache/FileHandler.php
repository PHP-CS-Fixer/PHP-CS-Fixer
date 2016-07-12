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

namespace PhpCsFixer\Cache;

use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class FileHandler implements FileHandlerInterface
{
    /**
     * @var string
     */
    private $file;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function read()
    {
        if (!file_exists($this->file)) {
            return;
        }

        $content = file_get_contents($this->file);

        try {
            $cache = Cache::fromJson($content);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        return $cache;
    }

    public function write(CacheInterface $cache)
    {
        $content = $cache->toJson();

        $bytesWritten = @file_put_contents($this->file, $content, LOCK_EX);

        if (false === $bytesWritten) {
            $error = error_get_last();

            if (null !== $error) {
                throw new IOException(sprintf('Failed to write file "%s", "%s".', $this->file, $error['message']), 0, null, $this->file);
            }

            throw new IOException(sprintf('Failed to write file "%s".', $this->file), 0, null, $this->file);
        }
    }
}
