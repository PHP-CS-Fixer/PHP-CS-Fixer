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

/**
 * Output writer for the result of FixCommand in JSON format.
 */
class JsonOutput extends AbstractFixerOutput
{
    private $json = array();

    protected function writeChange($file, array $fixResult)
    {
        $jFile = array('name' => $file);

        if (OutputInterface::VERBOSITY_VERBOSE <= $this->verbosity) {
            $jFile['appliedFixers'] = $fixResult['appliedFixers'];
        }

        if ($this->diff) {
            $jFile['diff'] = $fixResult['diff'];
        }

        $this->json['files'] = $jFile;
    }

    protected function writeError($error)
    {
        if (!isset($this->json['errors'])) {
            $this->json['errors'] = array();
        }

        $this->json['errors'][] = sprintf('%4d) %s', $this->errorCount, $error);
    }

    public function writeInfo($info)
    {
        if (!isset($this->json['info'])) {
            $this->json['info'] = array();
        }

        $this->json['info'][] = $info;
    }

    public function writeTimings(Stopwatch $stopwatch)
    {
        $fixEvent = $stopwatch->getEvent('fixFiles');
        $this->json['memory'] = round($fixEvent->getMemory() / 1024 / 1024, 3);
        $this->json['time'] = array('total' => round($fixEvent->getDuration() / 1000, 3));

        if (OutputInterface::VERBOSITY_DEBUG <= $this->verbosity) {
            $jFileTime = array();

            foreach ($stopwatch->getSectionEvents('fixFile') as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $jFileTime[$file] = round($event->getDuration() / 1000, 3);
            }
            $this->json['time']['files'] = $jFileTime;
        }
    }

    public function __destruct()
    {
        $this->output->write(json_encode($this->json));
    }
}
