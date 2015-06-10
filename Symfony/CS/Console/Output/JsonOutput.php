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

/**
 * Writes Fixer output in JSON format to the output.
 */
final class JsonOutput extends AbstractOutput
{
    public function onFixerFinished(FixerFinishedEvent $event)
    {
        $fixFiles = array();

        $changed = $event->getChanged();
        foreach ($changed as $file => $fixResult) {
            $fileInfo = array('name' => $file);

            if (OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity()) {
                $fileInfo['appliedFixers'] = $fixResult['appliedFixers'];
            }

            if ($event->isDiff()) {
                $fileInfo['diff'] = $fixResult['diff'];
            }

            $fixFiles[] = $fileInfo;
        }

        $json = array(
            'files' => $fixFiles,
            'memory' => round($event->getMemoryUsed() / 1024 / 1024, 3),
            'time' => array(
                'total' => round($event->getDuration() / 1000, 3),
            ),
        );

        if (OutputInterface::VERBOSITY_DEBUG <= $this->output->getVerbosity()) {
            $fileTime = array();
            $stopwatch = $event->getStopWatch();
            foreach ($stopwatch->getSectionEvents('fixFile') as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $fileTime[$file] = round($event->getDuration() / 1000, 3);
            }

            $json['time']['files'] = $fileTime;
        }

        $this->output->write(json_encode($json));
    }

    public static function getSubscribedEvents()
    {
        return array(
            FixerFinishedEvent::NAME => 'onFixerFinished',
        );
    }
}
