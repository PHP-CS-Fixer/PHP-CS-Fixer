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

    public function __construct(OutputInterface $output, ConfigInterface $config, $isDryRun, $diff)
    {
        $this->output = $output;
        $this->config = $config;
        $this->isDryRun = $isDryRun;
        $this->diff = $diff;
        $this->verbosity = $output->getVerbosity();
    }
}
