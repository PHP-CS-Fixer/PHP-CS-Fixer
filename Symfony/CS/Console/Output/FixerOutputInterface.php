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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Interface definition for output classes.
 */
interface FixerOutputInterface extends EventSubscriberInterface
{
    /**
     * @param OutputInterface $output
     *
     * @return FixerOutputInterface
     */
    public function setOutput(OutputInterface $output);

    /**
     * @param $diff bool
     *
     * @return FixerOutputInterface
     */
    public function setDiff($diff);
}
