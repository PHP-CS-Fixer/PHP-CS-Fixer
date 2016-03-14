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

use PhpCsFixer\ReportInterface;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
class TextReport implements ReportInterface
{
    /** @var array */
    private $changed = array();

    /** @var bool */
    private $addAppliedFixers = false;

    /** @var bool */
    private $isDryRun = false;

    /** @var bool */
    private $isDecoratedOutput = false;

    /** @var int */
    private $time;

    /** @var int */
    private $memory;

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'txt';
    }

    /**
     * {@inheritdoc}
     */
    public function setChanged(array $changed)
    {
        $this->changed = $changed;
    }

    /**
     * @param bool $addAppliedFixers
     *
     * @return $this
     */
    public function setAddAppliedFixers($addAppliedFixers)
    {
        $this->addAppliedFixers = $addAppliedFixers;

        return $this;
    }

    /**
     * @param bool $isDryRun
     *
     * @return $this
     */
    public function setIsDryRun($isDryRun)
    {
        $this->isDryRun = $isDryRun;

        return $this;
    }

    /**
     * @param bool $isDecoratedOutput
     *
     * @return $this
     */
    public function setIsDecoratedOutput($isDecoratedOutput)
    {
        $this->isDecoratedOutput = $isDecoratedOutput;

        return $this;
    }

    /**
     * @param int $time
     *
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @param int $memory
     *
     * @return $this
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $output = '';

        $i = 1;
        foreach ($this->changed as $file => $fixResult) {
            $output .= $this->getFile($file, $i++);
            $output .= $this->getAppliedFixers($fixResult);
            $output .= $this->getDiff($fixResult);
            $output .= PHP_EOL;
        }

        $output .= $this->getFooter();

        return $output;
    }

    /**
     * @param array $fixResult
     *
     * @return string
     */
    private function getFile($file, $i)
    {
        return PHP_EOL.sprintf('%4d) %s', $i, $file);
    }

    /**
     * @param array $fixResult
     *
     * @return string
     */
    private function getAppliedFixers($fixResult)
    {
        if (!$this->addAppliedFixers || empty($fixResult['appliedFixers'])) {
            return '';
        }

        $template = $this->isDecoratedOutput ? ' (<comment>%s</comment>)' : ' (%s)';

        return sprintf(
            $template,
            implode(', ', $fixResult['appliedFixers'])
        );
    }

    /**
     * @param array $fixResult
     *
     * @return string
     */
    private function getDiff($fixResult)
    {
        if (empty($fixResult['diff'])) {
            return '';
        }

        $template = '';

        if ($this->isDecoratedOutput) {
            $template .= '<comment>      ---------- begin diff ----------</comment>';
            $template .= PHP_EOL.'%s'.PHP_EOL;
            $template .= '<comment>      ----------- end diff -----------</comment>';
        } else {
            $template .= '      ---------- begin diff ----------';
            $template .= PHP_EOL.'%s'.PHP_EOL;
            $template .= '      ----------- end diff -----------';
        }

        return PHP_EOL.sprintf(
            $template,
            trim($fixResult['diff'])
        );
    }

    /**
     * @return string
     */
    private function getFooter()
    {
        if ($this->time === null || $this->memory === null) {
            return '';
        }

        return PHP_EOL.sprintf(
            '%s all files in %.3f seconds, %.3f MB memory used'.PHP_EOL,
            $this->isDryRun ? 'Checked' : 'Fixed',
            $this->time / 1000,
            $this->memory / 1024 / 1024
        );
    }
}
