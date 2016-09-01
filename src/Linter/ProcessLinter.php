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

namespace PhpCsFixer\Linter;

use PhpCsFixer\ShutdownFileRemoval;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

/**
 * Handle PHP code linting process.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ProcessLinter implements LinterInterface
{
    /**
     * Temporary file for code linting.
     *
     * @var string|null
     */
    private $temporaryFile;

    /**
     * PHP executable.
     *
     * @var string
     */
    private $executable;

    /**
     * Shutdown files removal handler.
     *
     * @var ShutdownFileRemoval
     */
    private $shutdownFileRemoval;

    /**
     * @param string|null $executable PHP executable, null for autodetection
     */
    public function __construct($executable = null)
    {
        if (null === $executable) {
            $executableFinder = new PhpExecutableFinder();
            $executable = $executableFinder->find(false);

            if (false === $executable) {
                throw new UnavailableLinterException('Cannot find PHP executable.');
            }
        }

        $this->executable = $executable;

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
     * {@inheritdoc}
     */
    public function isAsync()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function lintFile($path)
    {
        return new ProcessLintingResult($this->createProcessForFile($path));
    }

    /**
     * {@inheritdoc}
     */
    public function lintSource($source)
    {
        return new ProcessLintingResult($this->createProcessForSource($source));
    }

    /**
     * Create process that lint PHP file.
     *
     * @param string $path path to file
     *
     * @return Process
     */
    private function createProcessForFile($path)
    {
        // in case php://stdin
        if (!is_file($path)) {
            return $this->createProcessForSource(file_get_contents($path));
        }

        $process = new Process($this->prepareCommand($path));
        $process->setTimeout(null);
        $process->start();

        return $process;
    }

    /**
     * Create process that lint PHP code.
     *
     * @param string $source code
     *
     * @return Process
     */
    private function createProcessForSource($source)
    {
        if (null === $this->temporaryFile) {
            $this->temporaryFile = tempnam('.', 'cs_fixer_tmp_');
            $this->shutdownFileRemoval->attach($this->temporaryFile);
        }

        if (false === @file_put_contents($this->temporaryFile, $source)) {
            throw new IOException(sprintf('Failed to write file "%s".', $this->temporaryFile), 0, null, $this->temporaryFile);
        }

        return $this->createProcessForFile($this->temporaryFile);
    }

    /**
     * Prepare command that will lint a file.
     *
     * @param string $path
     *
     * @return string
     */
    private function prepareCommand($path)
    {
        $executable = ProcessUtils::escapeArgument($this->executable);

        if (defined('HHVM_VERSION')) {
            $executable .= ' --php';
        }

        $path = ProcessUtils::escapeArgument($path);

        return sprintf('%s -l %s', $executable, $path);
    }
}
