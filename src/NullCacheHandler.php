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

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class NullCacheHandler implements CacheHandler
{
    public function willCache()
    {
        return false;
    }

    public function read()
    {
    }

    public function write($content)
    {
    }
}
