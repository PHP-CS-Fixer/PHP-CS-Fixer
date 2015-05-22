<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\CS\ConfigInterface;

/**
 * Abstract base class for FixerOutputInterface.
 */
abstract class AbstractFixerOutput implements FixerOutputInterface
{
    /**
     * Config instance.
     *
     * @var ConfigInterface
     */
    protected $config;

    /**
     * OutputInterface implementation.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * Verbosity level.
     *
     * @var int
     */
    protected $verbosity;

    /**
     * Is dry run.
     *
     * @var bool
     */
    protected $isDryRun;

    /**
     * Output diff.
     *
     * @var bool
     */
    protected $diff;

    protected $errorCount = 0;

    protected $changeCount = 0;

    public function __construct(OutputInterface $output, ConfigInterface $config, $isDryRun, $diff)
    {
        $this->output = $output;
        $this->config = $config;
        $this->isDryRun = $isDryRun;
        $this->diff = $diff;
        $this->verbosity = $output->getVerbosity();
    }

    abstract protected function writeChange($file, array $fixResult);

    abstract protected function writeError($error);

    public function writeChanges(array $changes)
    {
        foreach ($changes as $file => $fixResult) {
            $this->changeCount++;
            $this->writeChange($file, $fixResult);
        }
    }

    public function writeErrors(array $errors)
    {
        foreach ($errors as $i => $error) {
            $this->errorCount++;
            $this->writeError($error['filePath']); // FIXME array access thing here
        }
    }
}
