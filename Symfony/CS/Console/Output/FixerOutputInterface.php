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
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\CS\ConfigInterface;

interface FixerOutputInterface
{
    /**
     * @param OutputInterface $output   A OutputInterface instance
     * @param ConfigInterface $config   A ConfigInterface instance
     * @param bool            $isDryRun Whether the changes are simulated or not
     * @param bool            $diff     Whether to write a diff or not
     */
    public function __construct(OutputInterface $output, ConfigInterface $config, $isDryRun, $diff);

    public function writeChanges(array $changes);

    public function writeError($error);

    public function writeErrors(array $errors);

    public function writeInfo($info);

    public function writeTimings(Stopwatch $stopwatch);
}
