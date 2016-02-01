<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Console\Output;

use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Output writer to show the process of a FixCommand.
 *
 * @internal
 */
final class ProcessOutput
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

    /**
     * Event dispatcher instance.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Stream output instance.
     *
     * @var StreamOutput
     */
    private $output;

    /**
     * File pointer of type stream.
     *
     * @var resource
     */
    private $stdErr;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->stdErr = fopen('php://stderr', 'w');
        $this->output = new StreamOutput($this->stdErr);
        $this->eventDispatcher = $dispatcher;
        $this->eventDispatcher->addListener(FixerFileProcessedEvent::NAME, array($this, 'onFixerFileProcessed'));
    }

    public function onFixerFileProcessed(FixerFileProcessedEvent $event)
    {
        $status = self::$eventStatusMap[$event->getStatus()];
        $this->output->write($this->output->isDecorated() ? sprintf($status['format'], $status['symbol']) : $status['symbol']);
    }

    public function printLegend()
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
    }

    public function __destruct()
    {
        fclose($this->stdErr);
        $this->eventDispatcher->removeListener(FixerFileProcessedEvent::NAME, array($this, 'onFixerFileProcessed'));
    }
}
