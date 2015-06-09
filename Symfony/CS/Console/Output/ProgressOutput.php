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

use Symfony\CS\Events\FixerFileProcessedEvent;
use Symfony\CS\Events\FixerFinishedEvent;

/**
 * Output writer to show the progress of a FixCommand.
 *
 * @internal
 */
final class ProgressOutput extends AbstractOutput
{
    /**
     * File statuses map.
     *
     * @var array
     */
    private static $eventStatusMap = array(
        FixerFileProcessedEvent::STATUS_UNKNOWN => array('symbol' => '?', 'format' => '%s', 'description' => 'unknown'),
        FixerFileProcessedEvent::STATUS_INVALID => array('symbol' => 'I', 'format' => '<bg=red>%s</bg=red>', 'description' => 'invalid file syntax, file ignored'),
        FixerFileProcessedEvent::STATUS_SKIPPED => array('symbol' => 'S', 'format' => '<fg=cyan>%s</fg=cyan>', 'description' => 'Skipped'),
        FixerFileProcessedEvent::STATUS_NO_CHANGES => array('symbol' => '.', 'format' => '%s', 'description' => 'no changes'),
        FixerFileProcessedEvent::STATUS_FIXED => array('symbol' => 'F', 'format' => '<fg=green>%s</fg=green>', 'description' => 'fixed'),
        FixerFileProcessedEvent::STATUS_EXCEPTION => array('symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'error'),
        FixerFileProcessedEvent::STATUS_LINT => array('symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'error'),
    );

    public function onFixerFileProcessed(FixerFileProcessedEvent $event)
    {
        $status = self::$eventStatusMap[$event->getStatus()];
        $this->output->write($this->output->isDecorated() ? sprintf($status['format'], $status['symbol']) : $status['symbol']);
    }

    public function onFixerFinished(FixerFinishedEvent $event)
    {
        $symbols = array();

        foreach (self::$eventStatusMap as $status) {
            $symbol = $status['symbol'];
            if ('' === $symbol || isset($symbols[$symbol])) {
                continue;
            }

            $symbols[$symbol] = sprintf('%s-%s', $this->output->isDecorated() ? sprintf($status['format'], $symbol) : $symbol, $status['description']);
        }

        $this->output->write(sprintf("\nLegend: %s\n", implode(', ', $symbols)));
        $this->output->writeln('');
    }

    public static function getSubscribedEvents()
    {
        return array(
            FixerFileProcessedEvent::NAME => 'onFixerFileProcessed',
            FixerFinishedEvent::NAME => 'onFixerFinished',
        );
    }
}
