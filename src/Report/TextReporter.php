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

use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class TextReporter implements ReporterInterface
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
    public function generate(ReportSummary $reportSummary)
    {
        $output = '';

        $i = 0;
        foreach ($reportSummary->getChanged() as $file => $fixResult) {
            ++$i;
            $output .= sprintf('%4d) %s', $i, $file);

            if ($reportSummary->shouldAddAppliedFixers()) {
                $output .= $this->getAppliedFixers($reportSummary->isDecoratedOutput(), $fixResult);
            }

            $output .= $this->getDiff($reportSummary->isDecoratedOutput(), $fixResult);
            $output .= PHP_EOL;
        }

        return $output.$this->getFooter($reportSummary->getTime(), $reportSummary->getMemory(), $reportSummary->isDryRun());
    }

    /**
     * @param bool  $isDecoratedOutput
     * @param array $fixResult
     *
     * @return string
     */
    private function getAppliedFixers($isDecoratedOutput, array $fixResult)
    {
        return sprintf(
            $isDecoratedOutput ? ' (<comment>%s</comment>)' : ' (%s)',
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

        if ($isDecoratedOutput) {
            $template = '<comment>      ---------- begin diff ----------</comment>%s<comment>      ----------- end diff -----------</comment>';
            $diff = implode(
                PHP_EOL,
                array_map(
                    function ($string) {
                        $string = preg_replace('/^(\+){3}/', '<info>+++</info>', $string);
                        $string = preg_replace('/^(\+){1}/', '<info>+</info>', $string);
                        $string = preg_replace('/^(\-){3}/', '<error>---</error>', $string);
                        $string = preg_replace('/^(\-){1}/', '<error>-</error>', $string);

                        return $string;
                    },
                    explode(PHP_EOL, OutputFormatter::escape($fixResult['diff']))
                )
            );
        } else {
            $template = '      ---------- begin diff ----------%s      ----------- end diff -----------';
            $diff = $fixResult['diff'];
        }

        return PHP_EOL.sprintf($template, PHP_EOL.$diff.PHP_EOL).PHP_EOL;
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
        if (0 === $time || 0 === $memory) {
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
