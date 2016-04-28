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

use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class FileCacheHandler implements CacheHandler
{
    /**
     * @var string
     */
    private $cacheFile;

    public function __construct($cacheFile)
    {
        $this->cacheFile = $cacheFile;
    }

    public function willCache()
    {
        return true;
    }

    public function read()
    {
        if (!file_exists($this->cacheFile)) {
            return;
        }

        return file_get_contents($this->cacheFile);
    }

    public function write($content)
    {
        $bytesWritten = @file_put_contents($this->cacheFile, $content, LOCK_EX);

        if (false === $bytesWritten) {
            throw new IOException(sprintf('Failed to write file "%s".', $this->cacheFile), 0, null, $this->cacheFile);
        }
    }
}
