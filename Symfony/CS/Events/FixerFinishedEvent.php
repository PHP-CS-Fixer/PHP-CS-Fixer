<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Events;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\CS\Error\ErrorsManager;

/**
 * Event that is fired when the Fixer is finished.
 */
final class FixerFinishedEvent extends Event
{
    const NAME = 'fixer.finished';

    private $changed;
    private $stopWatch;
    private $fixEvent;
    private $errors;
    private $dryRun;
    private $diff;

    /**
     * @param array         $changed
     * @param Stopwatch     $stopwatch
     * @param ErrorsManager $errors
     * @param bool          $dryRun
     * @param bool          $diff
     */
    public function __construct(array $changed, Stopwatch $stopwatch, ErrorsManager $errors, $dryRun, $diff)
    {
        $this->changed = $changed;
        $this->stopWatch = $stopwatch;
        $this->errors = $errors;
        $this->dryRun = $dryRun;
        $this->diff = $diff;
        $this->fixEvent = $stopwatch->getEvent('fixFiles');
    }

    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * Time in mini seconds it took to execute the Fixer.
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->fixEvent->getDuration();
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Amount of memory in byes used during the execution of the Fixer.
     *
     * @return int
     */
    public function getMemoryUsed()
    {
        return $this->fixEvent->getMemory();
    }

    public function getStopWatch()
    {
        return $this->stopWatch;
    }

    public function isDiff()
    {
        return $this->diff;
    }

    public function isDryRun()
    {
        return $this->dryRun;
    }
}
