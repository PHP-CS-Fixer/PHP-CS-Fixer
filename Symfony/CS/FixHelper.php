<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * Helps outputting status information when running the fix command.
 *
 * @author Eugene Leonovich <gen.work@gmail.com>
 */
class FixHelper
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @var bool
     */
    private $showProgress;

    /**
     * @var array
     */
    private $stats = array();

    /**
     * @param OutputInterface $output
     * @param Stopwatch       $stopwatch
     * @param bool|null       $showProgress
     */
    public function __construct(OutputInterface $output, Stopwatch $stopwatch, $showProgress = null)
    {
        $this->output = $output;
        $this->stopwatch = $stopwatch;
        $this->showProgress = (bool) $showProgress;
    }

    /**
     * @param callable                 $fixFiles
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return array
     */
    public function fixFiles(callable $fixFiles, EventDispatcherInterface $eventDispatcher)
    {
        $listener = array($this, 'onFileProcessed');

        $eventDispatcher->addListener(FixerFileProcessedEvent::NAME, $listener);
        $this->stopwatch->start('fixFiles');

        $result = $fixFiles();

        $this->stopwatch->stop('fixFiles');
        $eventDispatcher->removeListener(FixerFileProcessedEvent::NAME, $listener);

        if ($this->showProgress) {
            $this->displayLegend();
        }

        return $result;
    }

    /**
     * @param array $processedFiles
     * @param bool  $showDiff
     */
    public function displayResult(array $processedFiles, $showDiff)
    {
        $verbosity = $this->output->getVerbosity();

        $i = 1;
        foreach ($processedFiles as $file => $fixResult) {
            $this->output->write(sprintf('%4d) %s', $i++, $file));

            if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                $this->output->write(sprintf(' (<comment>%s</comment>)', implode(', ', $fixResult['appliedFixers'])));
            }

            if ($showDiff) {
                $this->output->writeln('');
                $this->output->writeln('<comment>      ---------- begin diff ----------</comment>');
                $this->output->writeln($fixResult['diff']);
                $this->output->writeln('<comment>      ---------- end diff ----------</comment>');
            }

            $this->output->writeln('');
        }

        if (OutputInterface::VERBOSITY_DEBUG <= $verbosity) {
            $this->output->writeln('');
            $this->output->writeln('Fixing time per file:');

            foreach ($this->stopwatch->getSectionEvents('fixFile') as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $this->output->writeln(sprintf('[%.3f s] %s', $event->getDuration() / 1000, $file));
            }

            $this->output->writeln('');
        }

        $fixEvent = $this->stopwatch->getEvent('fixFiles');
        $this->displaySummary($fixEvent);
    }

    /**
     * @param array $processedFiles
     * @param bool  $showDiff
     */
    public function displayResultInXml(array $processedFiles, $showDiff)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $filesXML = $dom->createElement('files');
        $dom->appendChild($filesXML);

        $verbosity = $this->output->getVerbosity();

        $i = 1;
        foreach ($processedFiles as $file => $fixResult) {
            $fileXML = $dom->createElement('file');
            $fileXML->setAttribute('id', $i++);
            $fileXML->setAttribute('name', $file);
            $filesXML->appendChild($fileXML);

            if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                $appliedFixersXML = $dom->createElement('applied_fixers');
                $fileXML->appendChild($appliedFixersXML);

                foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                    $appliedFixerXML = $dom->createElement('applied_fixer');
                    $appliedFixerXML->setAttribute('name', $appliedFixer);
                    $appliedFixersXML->appendChild($appliedFixerXML);
                }
            }

            if ($showDiff) {
                $diffXML = $dom->createElement('diff');
                $diffXML->appendChild($dom->createCDATASection($fixResult['diff']));
                $fileXML->appendChild($diffXML);
            }
        }

        $dom->formatOutput = true;
        $this->output->write($dom->saveXML());
    }

    /**
     * @param array $processedFiles
     * @param bool  $showDiff
     */
    public function displayResultInJson(array $processedFiles, $showDiff)
    {
        $verbosity = $this->output->getVerbosity();

        $jFiles = array();

        foreach ($processedFiles as $file => $fixResult) {
            $jfile = array('name' => $file);

            if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                $jfile['appliedFixers'] = $fixResult['appliedFixers'];
            }

            if ($showDiff) {
                $jfile['diff'] = $fixResult['diff'];
            }

            $jFiles[] = $jfile;
        }

        $fixEvent = $this->stopwatch->getEvent('fixFiles');

        $json = array(
            'files' => $jFiles,
            'memory' => round($fixEvent->getMemory() / 1024 / 1024, 3),
            'time' => array(
                'total' => round($fixEvent->getDuration() / 1000, 3),
            ),
        );

        if (OutputInterface::VERBOSITY_DEBUG <= $verbosity) {
            $jFileTime = array();

            foreach ($this->stopwatch->getSectionEvents('fixFile') as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $jFileTime[$file] = round($event->getDuration() / 1000, 3);
            }

            $json['time']['files'] = $jFileTime;
        }

        $this->output->write(json_encode($json));
    }

    /**
     * Process the event.
     *
     * @param FixerFileProcessedEvent $event
     */
    public function onFileProcessed(FixerFileProcessedEvent $event)
    {
        $symbol = $event->getStatusAsString();

        if (!isset($this->stats[$symbol])) {
            $this->stats[$symbol] = 0;
        }

        ++$this->stats[$symbol];

        if ($this->showProgress) {
            $this->output->write($event->getStatusAsString());
        }
    }

    private function displayLegend()
    {
        $this->output->writeln('');

        $legend = array();
        foreach (FixerFileProcessedEvent::getStatusMap() as $status) {
            if ($status['symbol'] && $status['description']) {
                $legend[] = $status['symbol'].'-'.$status['description'];
            }
        }

        $this->output->writeln('Legend: '.implode(', ', array_unique($legend)));
    }

    private function displaySummary(StopwatchEvent $fixEvent)
    {
        $this->output->writeln(sprintf('Processed all files in %.3f seconds, %.3f MB memory used',
            $fixEvent->getDuration() / 1000,
            $fixEvent->getMemory() / 1024 / 1024
        ));

        $this->output->writeln($this->getStatsAsString());
    }

    private function getStatsAsString()
    {
        $result = array('Total: '.array_sum($this->stats));

        foreach (FixerFileProcessedEvent::getStatusMap() as $status) {
            $symbol = $status['symbol'];

            if (!empty($this->stats[$symbol])) {
                $result[] = $status['name'].': '.$this->stats[$symbol];
            }
        }

        return implode(', ', $result);
    }
}
