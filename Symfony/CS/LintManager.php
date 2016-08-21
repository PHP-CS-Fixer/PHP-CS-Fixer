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

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

/**
 * Handle PHP code linting process.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class LintManager
{
    /**
     * Temporary file for code linting.
     *
     * @var string|null
     */
    private $temporaryFile;

    /**
     * Shutdown files removal handler.
     *
     * @var ShutdownFileRemoval
     */
    private $shutdownFileRemoval;

    public function __construct()
    {
        $this->shutdownFileRemoval = new ShutdownFileRemoval();
    }

    public function __destruct()
    {
        if (null !== $this->temporaryFile) {
            unlink($this->temporaryFile);
            $this->shutdownFileRemoval->detach($this->temporaryFile);
        }
    }

    /**
     * Create process that lint PHP file.
     *
     * @param string $path path to file
     *
     * @return Process
     */
    public function createProcessForFile($path)
    {
        // in case php://stdin
        if (!is_file($path)) {
            return $this->createProcessForSource(file_get_contents($path));
        }

        $process = new Process('php -l '.ProcessUtils::escapeArgument($path));
        $process->setTimeout(null);
        $process->run();

        return $process;
    }

    /**
     * Create process that lint PHP code.
     *
     * @param string $source code
     *
     * @return Process
     */
    public function createProcessForSource($source)
    {
        if (null === $this->temporaryFile) {
            $this->temporaryFile = tempnam('.', 'cs_fixer_tmp_');
            $this->shutdownFileRemoval->attach($this->temporaryFile);
        }

        file_put_contents($this->temporaryFile, $source);

        return $this->createProcessForFile($this->temporaryFile);
    }
}
