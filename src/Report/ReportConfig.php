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
final class ReportConfig
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
     * @return float
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param bool $addAppliedFixers
     *
     * @return ReportConfig
     */
    public function setAddAppliedFixers($addAppliedFixers)
    {
        $this->addAppliedFixers = $addAppliedFixers;

        return $this;
    }

    /**
     * @param array $changed
     *
     * @return ReportConfig
     */
    public function setChanged(array $changed)
    {
        $this->changed = $changed;

        return $this;
    }

    /**
     * @param bool $isDecoratedOutput
     *
     * @return ReportConfig
     */
    public function setIsDecoratedOutput($isDecoratedOutput)
    {
        $this->isDecoratedOutput = $isDecoratedOutput;

        return $this;
    }

    /**
     * @param bool $isDryRun
     *
     * @return ReportConfig
     */
    public function setIsDryRun($isDryRun)
    {
        $this->isDryRun = $isDryRun;

        return $this;
    }

    /**
     * @param float $memory
     *
     * @return ReportConfig
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * @param float $time
     *
     * @return ReportConfig
     */
    public function setTime($time)
    {
        $this->time = $time;

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
