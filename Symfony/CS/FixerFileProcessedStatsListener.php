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

/**
 * @author Eugene Leonovich <gen.work@gmail.com>
 */
class FixerFileProcessedStatsListener
{
    private $stats = array();

    /**
     * Process the event.
     *
     * @param FixerFileProcessedEvent $event
     */
    public function __invoke(FixerFileProcessedEvent $event)
    {
        $symbol = $event->getStatusAsString();

        if (!isset($this->stats[$symbol])) {
            $this->stats[$symbol] = 0;
        }

        ++$this->stats[$symbol];
    }

    /**
     * Get statistics as string.
     *
     * @return string
     */
    public function getStatsAsString()
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
