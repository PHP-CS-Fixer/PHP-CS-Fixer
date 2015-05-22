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
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\CS\ConfigInterface;

/**
 * Output writer for the result of FixCommand in XML format.
 */
class XmlOutput extends AbstractFixerOutput
{
    private $dom;
    private $errorsNode = null;
    private $filesNode = null;
    private $informationNode = null;

    public function __construct(OutputInterface $output, ConfigInterface $config, $isDryRun, $diff)
    {
        parent::__construct($output, $config, $isDryRun, $diff);
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
    }

    protected function writeChange($file, array $fixResult)
    {
        if (null === $this->filesNode) {
            $this->filesNode =  $this->dom->createElement('files');
            $this->dom->appendChild($this->filesNode);
        }

        $fileXML = $this->dom->createElement('file');
        $fileXML->setAttribute('id', $this->changeCount);
        $fileXML->setAttribute('name', $file);
        $this->filesNode->appendChild($fileXML);

        if (OutputInterface::VERBOSITY_VERBOSE <= $this->verbosity) {
            $appliedFixersXML = $this->dom->createElement('applied_fixers');
            $fileXML->appendChild($appliedFixersXML);

            foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                $appliedFixerXML = $this->dom->createElement('applied_fixer');
                $appliedFixerXML->setAttribute('name', $appliedFixer);
                $appliedFixersXML->appendChild($appliedFixerXML);
            }
        }

        if ($this->diff) {
            $diffXML = $this->dom->createElement('diff');
            $diffXML->appendChild($this->dom->createCDATASection($fixResult['diff']));
            $fileXML->appendChild($diffXML);
        }
    }

    protected function writeError($error)
    {
        if (null === $this->errorsNode) {
            $this->errorsNode =  $this->dom->createElement('errors');
            $this->dom->appendChild($this->errorsNode);
        }

        $errorXML = $this->dom->createElement('error');
        $errorXML->setAttribute('name', 'error');
        $errorXML->setAttribute('id', $this->errorCount);
        $errorXML->appendChild($this->dom->createCDATASection($error));
        $this->errorsNode->appendChild($errorXML);
    }

    public function writeInfo($info)
    {
        if (null === $this->informationNode) {
            $this->informationNode =  $this->dom->createElement('information');
            $this->dom->appendChild($this->informationNode);
        }

        $infoXML = $this->dom->createElement('info');
        $infoXML->setAttribute('name', 'info');
        $infoXML->appendChild($this->dom->createCDATASection($info));
        $this->informationNode->appendChild($infoXML);
    }

    public function writeTimings(Stopwatch $stopwatch)
    {
        $fixEvent = $stopwatch->getEvent('fixFiles');

        $timeXML = $this->dom->createElement('time');
        $memoryXML = $this->dom->createElement('memory');
        $this->dom->appendChild($timeXML);
        $this->dom->appendChild($memoryXML);

        $memoryXML->setAttribute('value', round($fixEvent->getMemory() / 1024 / 1024, 3));
        $memoryXML->setAttribute('unit', 'MB');

        $timeXML->setAttribute('unit', 's');
        $timeTotalXML = $this->dom->createElement('total');
        $timeTotalXML->setAttribute('value', round($fixEvent->getDuration() / 1000, 3));
        $timeXML->appendChild($timeTotalXML);

        if (OutputInterface::VERBOSITY_DEBUG <= $this->verbosity) {
            $timeFilesXML = $this->dom->createElement('files');
            $timeXML->appendChild($timeFilesXML);
            $eventCounter = 1;

            foreach ($this->stopwatch->getSectionEvents('fixFile') as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $timeFileXML = $this->dom->createElement('file');
                $timeFilesXML->appendChild($timeFileXML);
                $timeFileXML->setAttribute('id', $eventCounter++);
                $timeFileXML->setAttribute('name', $file);
                $timeFileXML->setAttribute('value', round($event->getDuration() / 1000, 3));
            }
        }
    }

    public function __destruct()
    {
        $this->dom->formatOutput = true;
        $this->output->write($this->dom->saveXML());
    }
}
