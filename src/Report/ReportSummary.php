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

namespace PhpCsFixer\Report;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ReportSummary
{
    /**
     * @var bool
     */
    private $addAppliedFixers = false;

    /**
     * @var array
     */
    private $changed = array();

    /**
     * @var bool
     */
    private $isDecoratedOutput = false;

    /**
     * @var bool
     */
    private $isDryRun = false;

    /**
     * @var float
     */
    private $memory;

    /**
     * @var float
     */
    private $time;

    /**
     * @return ReportSummary
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @return bool
     */
    public function isDecoratedOutput()
    {
        return $this->isDecoratedOutput;
    }

    /**
     * @return bool
     */
    public function isDryRun()
    {
        return $this->isDryRun;
    }

    /**
     * @return array
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param array $changed
     *
     * @return ReportSummary
     */
    public function setChanged(array $changed)
    {
        $this->changed = $changed;

        return $this;
    }

    /**
     * @return float
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * @param float $memory
     *
     * @return ReportSummary
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param float $time
     *
     * @return ReportSummary
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @param bool $addAppliedFixers
     *
     * @return ReportSummary
     */
    public function setAddAppliedFixers($addAppliedFixers)
    {
        $this->addAppliedFixers = $addAppliedFixers;

        return $this;
    }

    /**
     * @param bool $isDecoratedOutput
     *
     * @return ReportSummary
     */
    public function setDecoratedOutput($isDecoratedOutput)
    {
        $this->isDecoratedOutput = $isDecoratedOutput;

        return $this;
    }

    /**
     * @param bool $isDryRun
     *
     * @return ReportSummary
     */
    public function setDryRun($isDryRun)
    {
        $this->isDryRun = $isDryRun;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldAddAppliedFixers()
    {
        return $this->addAppliedFixers;
    }
}
