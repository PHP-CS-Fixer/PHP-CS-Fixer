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

use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;

/**
 * @author Davi Koscianski Vidal <davividal@gmail.com>
 */
class STDINFileInfo extends \SplFileInfo
{
    const VERSION = '0.3-DEV';

    public function __construct()
    {
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function getRealpath()
    {
        return 'php://stdin';
    }

    public function getFilename()
    {
        return 'stdin.php';
    }

    public function getContents()
    {
        return file_get_contents($this->getRealpath());
    }

    public function isDir()
    {
        return false;
    }
}
