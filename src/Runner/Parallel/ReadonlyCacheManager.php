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

namespace PhpCsFixer\Runner\Parallel;

use PhpCsFixer\Cache\CacheManagerInterface;

final class ReadonlyCacheManager implements CacheManagerInterface
{
    private CacheManagerInterface $cacheManager;

    public function __construct(CacheManagerInterface $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function needFixing(string $file, string $fileContent): bool
    {
        return $this->cacheManager->needFixing($file, $fileContent);
    }

    public function setFile(string $file, string $fileContent): void {}

    public function setFileHash(string $file, string $hash): void {}
}
