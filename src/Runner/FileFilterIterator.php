<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Runner;

use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileFilterIterator extends \FilterIterator
{
    /**
     * @var null|EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var array<string,bool>
     */
    private $visitedElements = [];

    public function __construct(
        \Iterator $iterator,
        EventDispatcher $eventDispatcher = null,
        CacheManagerInterface $cacheManager
    ) {
        parent::__construct($iterator);

        $this->eventDispatcher = $eventDispatcher;
        $this->cacheManager = $cacheManager;
    }

    public function accept()
    {
        $file = $this->current();
        $path = $file->getRealPath();

        if (isset($this->visitedElements[$path])) {
            return false;
        }

        $this->visitedElements[$path] = true;

        if ($file->isDir() || $file->isLink()) {
            return false;
        }

        $content = file_get_contents($path);

        // mark as skipped:
        if (
            // empty file
            '' === $content
            // file that does not need fixing due to cache
            || !$this->cacheManager->needFixing($file->getPathname(), $content)
        ) {
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                new FixerFileProcessedEvent(FixerFileProcessedEvent::STATUS_SKIPPED)
            );

            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @param Event  $event
     */
    private function dispatchEvent($name, Event $event)
    {
        if (null === $this->eventDispatcher) {
            return;
        }

        $this->eventDispatcher->dispatch($name, $event);
    }
}
