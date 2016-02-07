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

use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class UniqueFileIterator extends \FilterIterator
{
    private $visitedElements = array();

    public function accept()
    {
        /** @var SplFileInfo $file */
        $file = $this->current();

        $path = $file->getRealPath();

        if (isset($this->visitedElements[$path])) {
            return false;
        }

        $this->visitedElements[$path] = true;

        return !$file->isDir() && !$file->isLink();
    }
}
