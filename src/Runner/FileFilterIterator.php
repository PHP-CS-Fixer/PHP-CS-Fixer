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
use PhpCsFixer\FileReader;
use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileFilterIterator extends \FilterIterator
{
    /**
     * @var null|EventDispatcherInterface
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
        EventDispatcherInterface $eventDispatcher = null,
        CacheManagerInterface $cacheManager
    ) {
        parent::__construct($iterator);

        $this->eventDispatcher = $eventDispatcher;
        $this->cacheManager = $cacheManager;
    }

    public function accept()
    {
        $file = $this->current();
        if (!$file instanceof \SplFileInfo) {
            throw new \RuntimeException(
                sprintf(
                    'Expected instance of "\SplFileInfo", got "%s".',
                    is_object($file) ? get_class($file) : gettype($file)
                )
            );
        }

        $path = $file->getRealPath();

        if (isset($this->visitedElements[$path])) {
            return false;
        }

        $this->visitedElements[$path] = true;

        if (!$file->isFile() || $file->isLink()) {
            return false;
        }

        $content = @FileReader::createSingleton()->read($path);
        if (false === $content) {
            $error = error_get_last();

            throw new \RuntimeException(sprintf(
                'Failed to read content from "%s".%s',
                $path,
                $error ? ' '.$error['message'] : ''
            ));
        }

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
