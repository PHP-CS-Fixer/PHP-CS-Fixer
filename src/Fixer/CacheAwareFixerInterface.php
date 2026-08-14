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

namespace PhpCsFixer\Fixer;

/**
 * Fixers whose output depends on external state beyond file content and
 * rule configuration (e.g. versions of external tools) should implement
 * this interface. The returned fingerprint is included in the cache
 * signature so that the cache is invalidated when the external state
 * changes.
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
interface CacheAwareFixerInterface extends FixerInterface
{
    /**
     * Return a fingerprint that represents the external state this fixer
     * depends on. When this value changes, the cache is invalidated.
     */
    public function getCacheFingerprint(): string;
}
