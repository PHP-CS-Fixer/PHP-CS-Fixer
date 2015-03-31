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

use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\CS\FixerFileProcessedEvent;

class ProcessOutput
{
    /**
     * File statuses map.
     *
     * @var array
     */
    private static $eventStatusMap = array(
        FixerFileProcessedEvent::STATUS_UNKNOWN    => array('symbol' => '?', 'format' => '%s', 'description' => 'unknown'),
        FixerFileProcessedEvent::STATUS_INVALID    => array('symbol' => 'I', 'format' => '<bg=red>%s</bg=red>',  'description' => 'invalid file syntax, file ignored'),
        FixerFileProcessedEvent::STATUS_SKIPPED    => array('symbol' => '', 'format' => '%s', 'description' => ''),
        FixerFileProcessedEvent::STATUS_NO_CHANGES => array('symbol' => '.', 'format' => '%s', 'description' => 'no changes'),
        FixerFileProcessedEvent::STATUS_FIXED      => array('symbol' => 'F', 'format' => '<bg=green>%s</bg=green>', 'description' => 'fixed'),
        FixerFileProcessedEvent::STATUS_EXCEPTION  => array('symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'error'),
        FixerFileProcessedEvent::STATUS_LINT       => array('symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'error'),
    );

    /**
     * StreamOutput instance.
     *
     * @var
     */
    private $output;

    /**
     * EventDispatcher instance.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcher $dispatcher
     * @param StreamOutput    $output
     */
    public function __construct(EventDispatcher $dispatcher, StreamOutput $output)
    {
        $this->output = $output;
        $this->eventDispatcher = $dispatcher;
        $this->eventDispatcher->addListener(FixerFileProcessedEvent::NAME, array($this, 'onFixerFileProcessed'));
    }

    public function onFixerFileProcessed(FixerFileProcessedEvent $event)
    {
        $status = self::$eventStatusMap[$event->getStatus()];
        if ($this->output->isDecorated()) {
            $symbol = sprintf($status['format'], $status['symbol']);
        } else {
            $symbol = $status['symbol'];
        }
        $symbol = sprintf($status['format'], $status['symbol']);

        $this->output->write($symbol);
    }

    public function printLegend()
    {
        $this->output->write("\nLegend:");
        $symbols = array();
        foreach (self::$eventStatusMap as $status) {
            if ('' === ($status['symbol'])) {
                continue;
            }

            if ($this->output->isDecorated()) {
                $symbol = sprintf($status['format'], $status['symbol']);
            } else {
                $symbol = $status['symbol'];
            }

            if (in_array($symbol, $symbols, true)) {
                continue;
            }
            $symbols[] = $symbol;
            $this->output->write(sprintf(' %s-%s,', $symbol, $status['description']));
        }
        $this->output->write("\n");
    }

    public function close()
    {
        $this->eventDispatcher->removeListener(FixerFileProcessedEvent::NAME, $this);
        $this->output = null;
        $this->eventDispatcher = null;
    }
}
