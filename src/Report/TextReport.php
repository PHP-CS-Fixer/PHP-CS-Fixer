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
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
class TextReport implements ReportInterface
{
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
    public function generate(ReportConfig $reportConfig)
    {
        $output = '';

        $i = 0;
        foreach ($reportConfig->getChanged() as $file => $fixResult) {
            ++$i;
            $output .= sprintf('%4d) %s', $i, $file);

            if ($reportConfig->shouldAddAppliedFixers()) {
                $output .= $this->getAppliedFixers($reportConfig->isDecoratedOutput(), $fixResult);
            }

            $output .= $this->getDiff($reportConfig->isDecoratedOutput(), $fixResult);
            $output .= PHP_EOL;
        }

        $output .= $this->getFooter($reportConfig->getTime(), $reportConfig->getMemory(), $reportConfig->isDryRun());

        return $output;
    }

    /**
     * @param bool  $isDecoratedOutput
     * @param array $fixResult
     *
     * @return string
     */
    private function getAppliedFixers($isDecoratedOutput, array $fixResult)
    {
        if (empty($fixResult['appliedFixers'])) {
            return '';
        }

        $template = $isDecoratedOutput ? ' (<comment>%s</comment>)' : ' (%s)';

        return sprintf(
            $template,
            implode(', ', $fixResult['appliedFixers'])
        );
    }

    /**
     * @param bool  $isDecoratedOutput
     * @param array $fixResult
     *
     * @return string
     */
    private function getDiff($isDecoratedOutput, array $fixResult)
    {
        if (empty($fixResult['diff'])) {
            return '';
        }

        $template = '';

        if ($isDecoratedOutput) {
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
            $fixResult['diff']
        ).PHP_EOL;
    }

    /**
     * @param float $time
     * @param float $memory
     * @param bool  $isDryRun
     *
     * @return string
     */
    private function getFooter($time, $memory, $isDryRun)
    {
        if ($time === null || $memory === null) {
            return '';
        }

        return PHP_EOL.sprintf(
            '%s all files in %.3f seconds, %.3f MB memory used'.PHP_EOL,
            $isDryRun ? 'Checked' : 'Fixed',
            $time / 1000,
            $memory / 1024 / 1024
        );
    }
}
