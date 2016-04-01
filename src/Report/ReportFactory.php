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

use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class ReportFactory
{
    /** @var ReportInterface[] */
    private $reports = array();

    public static function create()
    {
        return new self();
    }

    public function registerBuiltInReports()
    {
        /** @var string[] $builtInReports */
        static $builtInReports;

        if (null === $builtInReports) {
            $builtInReports = array();

            /** @var SplFileInfo $file */
            foreach (SymfonyFinder::create()->files()->name('*Report.php')->in(__DIR__) as $file) {
            $relativeNamespace = $file->getRelativePath();
            $builtInReports[] = sprintf('%s\\%s%s', __NAMESPACE__, $relativeNamespace ? $relativeNamespace.'\\' : '', $file->getBasename('.php'));
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
     * @param string $format
     *
     * @return ReportInterface
     */
    public function getReport($format)
    {
        if (!isset($this->reports[$format])) {
            throw new \UnexpectedValueException(sprintf('Report for format "%s" is not registered.', $format));
        }

        return $this->reports[$format];
    }
}
