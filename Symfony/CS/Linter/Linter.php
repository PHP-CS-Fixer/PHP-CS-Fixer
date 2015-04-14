<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Linter;

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
class Linter implements LinterInterface
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
     * @param string|null $phpExecutable PHP executable, null for autodetection
     */
    public function __construct($phpExecutable = null)
    {
        if (null === $phpExecutable) {
            $executableFinder = new PhpExecutableFinder();
            $executable = $executableFinder->find();
        }

        if (empty($executable)) {
            throw new UnavailableLinterException();
        }

        $this->executable = $executable;
    }

    public function __destruct()
    {
        if (null !== $this->temporaryFile) {
            unlink($this->temporaryFile);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function lintFile($path)
    {
        $this->checkProcess($this->createProcessForFile($path));
    }

    /**
     * {@inheritdoc}
     */
    public function lintSource($source)
    {
        $this->checkProcess($this->createProcessForSource($source));
    }

    /**
     * Check if linting process was successful and raise LintingException if not.
     *
     * @param Process $process
     */
    private function checkProcess(Process $process)
    {
        if (!$process->isSuccessful()) {
            throw new LintingException($process->getOutput(), $process->getExitCode());
        }
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

        $process = new Process(sprintf('%s -l %s', $this->executable, ProcessUtils::escapeArgument($path)));
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
    private function createProcessForSource($source)
    {
        if (null === $this->temporaryFile) {
            $this->temporaryFile = tempnam('.', 'tmp');
        }

        file_put_contents($this->temporaryFile, $source);
        $process = $this->createProcessForFile($this->temporaryFile);

        return $process;
    }
}
