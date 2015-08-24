<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\FixerFileProcessedEvent;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\CS\FixerFileProcessedEvent;

/**
 * Custom logger implementation. Used only for test.
 */
class CustomFixerLogger
{
    /**
     * Event dispatcher instance.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $filesStatuses = array();

    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
        $this->eventDispatcher->addListener(FixerFileProcessedEvent::NAME, array($this, 'onFixerFileProcessed'));
    }

    /**
     * @param FixerFileProcessedEvent $event
     */
    public function onFixerFileProcessed(FixerFileProcessedEvent $event)
    {
        $name = $event->getFileInfo() ? $event->getFileInfo()->getFilename() : null;
        $this->filesStatuses[$event->getStatus()][] = $name;
    }

    /**
     * @return array
     */
    public function getFileStatuses()
    {
        return $this->filesStatuses;
    }

    /**
     * 
     */
    public function __destruct()
    {
        $this->eventDispatcher->removeListener(FixerFileProcessedEvent::NAME, array($this, 'onFixerFileProcessed'));
    }
}
