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

/**
 * Abstract base class for output classes.
 */
abstract class AbstractOutput implements FixerOutputInterface
{
    /**
     * Print diff.
     *
     * @var bool
     */
    protected $diff;

    /**
     * Stream output instance.
     *
     * @var OutputInterface
     */
    protected $output;

    public function setDiff($diff)
    {
        $this->diff = $diff;

        return $this;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }
}
