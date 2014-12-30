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

/**
 * @author Davi Koscianski Vidal <davividal@gmail.com>
 *
 * @internal
 */
class StdinFileInfo extends \SplFileInfo
{
    public function __construct()
    {
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function getRealpath()
    {
        // So file_get_contents & friends will work.
        return 'php://stdin';
    }

    public function getContents()
    {
        return file_get_contents($this->getRealpath());
    }

    public function getATime()
    {
        return 0;
    }

    public function getBasename($suffix = null)
    {
        return $this->getFilename();
    }

    public function getCTime()
    {
        return 0;
    }

    public function getExtension()
    {
        return '.php';
    }

    public function getFileInfo($class_name = null)
    {
        throw new \RuntimeException("Not implemented");
    }

    public function getFilename()
    {
        /*
         * Useful so fixers depending on PHP-only files still work.
         *
         * The idea to use STDIN is to parse PHP-only files, so we can
         * assume that there will be always a PHP file out there.
         */

        return 'stdin.php';
    }

    public function getGroup()
    {
        return 0;
    }

    public function getInode()
    {
        return 0;
    }

    public function getLinkTarget()
    {
        return '';
    }

    public function getMTime()
    {
        return 0;
    }

    public function getOwner()
    {
        return 0;
    }

    public function getPath()
    {
        return '';
    }

    public function getPathInfo($class_name = null)
    {
        throw new \RuntimeException("Not implemented");
    }

    public function getPathname()
    {
        return $this->getFilename();
    }

    public function getPerms()
    {
        return 0;
    }

    public function getSize()
    {
        return 0;
    }

    public function getType()
    {
        return 'file';
    }

    public function isDir()
    {
        return false;
    }

    public function isExecutable()
    {
        return false;
    }

    public function isFile()
    {
        return true;
    }

    public function isLink()
    {
        return false;
    }

    public function isReadable()
    {
        return true;
    }

    public function isWritable()
    {
        return false;
    }

    public function openFile($open_mode = 'r', $use_include_path = false, $context = null)
    {
        throw new \RuntimeException("Not implemented");
    }

    public function setFileClass($class_name = null)
    {
    }

    public function setInfoClass($class_name = null)
    {
    }
}
