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

namespace Symfony\CS;

/**
 * Handles left over temporary files removal.
 *
 * @author Adam Klvač <adam@klva.cz>
 *
 * @internal
 */
final class ShutdownFileRemoval
{
    /**
     * List of files to be removed.
     *
     * @var array
     */
    private $files = array();

    public function __construct()
    {
        register_shutdown_function(array($this, 'clean'));
    }

    /**
     * Adds a file to be removed.
     *
     * @param string $path
     */
    public function attach($path)
    {
        $this->files[] = $path;
    }

    /**
     * Removes a file from shutdown removal.
     *
     * @param string $path
     */
    public function detach($path)
    {
        $key = array_search($path, $this->files, true);

        if ($key) {
            unset($this->files[$key]);
        }
    }

    /**
     * Removes attached files.
     */
    public function clean()
    {
        foreach ($this->files as $file) {
            @unlink($file); // @ - file might be deleted already
        }

        $this->files = array();
    }
}
