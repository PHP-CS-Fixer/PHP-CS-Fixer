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

namespace PhpCsFixer;

use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class ReportBuilder
{
    /** @var ReportInterface[] */
    private $reports = array();

    /** @var string */
    private $format;

    /** @var array */
    private $changed = array();

    private $options = array(
        'isDryRun' => false,
        'isDecoratedOutput' => false,
        'addAppliedFixers' => false,
        'time' => null,
        'memory' => null,
    );

    public function registerBuiltInReports()
    {
        /** @var string[] $builtInReports */
        static $builtInReports;

        if (null === $builtInReports) {
            $builtInReports = array();

            /** @var SplFileInfo $file */
            foreach (SymfonyFinder::create()->files()->name('*Report.php')->in(__DIR__.'/Report') as $file) {
                $relativeNamespace = $file->getRelativePath();
                $builtInReports[] = 'PhpCsFixer\\Report\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename(
                        '.php'
                    );
            }
        }

        foreach ($builtInReports as $reportClass) {
            $this->registerReport(new $reportClass());
        }

        return $this;
    }

    /**
     * @param ReportInterface $report
     *
     * @return $this
     */
    public function registerReport(ReportInterface $report)
    {
        $format = $report->getFormat();

        if (isset($this->reports[$format])) {
            throw new \UnexpectedValueException(sprintf('Report for format "%s" is already registered.', $format));
        }

        $this->reports[$format] = $report;

        return $this;
    }

    /**
     * @param bool $isDryRun
     *
     * @return $this
     */
    public function setIsDryRun($isDryRun)
    {
        $this->options['isDryRun'] = $isDryRun;

        return $this;
    }

    /**
     * @param bool $isDecoratedOutput
     *
     * @return $this
     */
    public function setIsDecoratedOutput($isDecoratedOutput)
    {
        $this->options['isDecoratedOutput'] = $isDecoratedOutput;

        return $this;
    }

    /**
     * @param bool $addAppliedFixers
     *
     * @return $this
     */
    public function setAddAppliedFixers($addAppliedFixers)
    {
        $this->options['addAppliedFixers'] = $addAppliedFixers;

        return $this;
    }

    /**
     * @param int $time
     *
     * @return $this
     */
    public function setTime($time)
    {
        $this->options['time'] = $time;

        return $this;
    }

    /**
     * @param int $memory
     *
     * @return $this
     */
    public function setMemory($memory)
    {
        $this->options['memory'] = $memory;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param array $changed
     *
     * @return $this
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return ReportInterface
     */
    public function getReport()
    {
        if (!isset($this->reports[$this->format])) {
            throw new \UnexpectedValueException(sprintf('Report for format "%s" does not registered.', $this->format));
        }

        $report = $this->reports[$this->format];

        foreach ($this->options as $option => $value) {
            $setter = 'set'.ucfirst($option);
            if (method_exists($report, $setter)) {
                $report->$setter($value);
            }
        }

        $report->setChanged($this->changed);

        return $report;
    }
}
