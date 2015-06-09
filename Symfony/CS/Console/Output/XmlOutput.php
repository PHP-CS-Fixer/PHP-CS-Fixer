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
use Symfony\CS\Events\FixerFinishedEvent;

final class XmlOutput extends AbstractOutput
{
    public function onFixerFinished(FixerFinishedEvent $event)
    {
        $i = 0;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $filesXML = $dom->createElement('files');
        $dom->appendChild($filesXML);

        $changed = $event->getChanged();
        foreach ($changed as $file => $fixResult) {
            $fileXML = $dom->createElement('file');
            $fileXML->setAttribute('id', $i++);
            $fileXML->setAttribute('name', $file);
            $filesXML->appendChild($fileXML);

            if (OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity()) {
                $appliedFixersXML = $dom->createElement('applied_fixers');
                $fileXML->appendChild($appliedFixersXML);

                foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                    $appliedFixerXML = $dom->createElement('applied_fixer');
                    $appliedFixerXML->setAttribute('name', $appliedFixer);
                    $appliedFixersXML->appendChild($appliedFixerXML);
                }
            }

            if ($event->isDiff()) {
                $diffXML = $dom->createElement('diff');
                $diffXML->appendChild($dom->createCDATASection($fixResult['diff']));
                $fileXML->appendChild($diffXML);
            }
        }

        $stopwatch = $event->getStopWatch();
        $fixEvent = $stopwatch->getEvent('fixFiles');

        $timeXML = $dom->createElement('time');
        $memoryXML = $dom->createElement('memory');
        $dom->appendChild($timeXML);
        $dom->appendChild($memoryXML);

        $memoryXML->setAttribute('value', round($fixEvent->getMemory() / 1024 / 1024, 3));
        $memoryXML->setAttribute('unit', 'MB');

        $timeXML->setAttribute('unit', 's');
        $timeTotalXML = $dom->createElement('total');
        $timeTotalXML->setAttribute('value', round($fixEvent->getDuration() / 1000, 3));
        $timeXML->appendChild($timeTotalXML);

        if (OutputInterface::VERBOSITY_DEBUG <= $this->output->getVerbosity()) {
            $timeFilesXML = $dom->createElement('files');
            $timeXML->appendChild($timeFilesXML);
            $eventCounter = 1;

            foreach ($stopwatch->getSectionEvents('fixFile') as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $timeFileXML = $dom->createElement('file');
                $timeFilesXML->appendChild($timeFileXML);
                $timeFileXML->setAttribute('id', $eventCounter++);
                $timeFileXML->setAttribute('name', $file);
                $timeFileXML->setAttribute('value', round($event->getDuration() / 1000, 3));
            }
        }

        $dom->formatOutput = true;
        $this->output->write($dom->saveXML());
    }

    public static function getSubscribedEvents()
    {
        return array(
            FixerFinishedEvent::NAME => 'onFixerFinished',
        );
    }
}
